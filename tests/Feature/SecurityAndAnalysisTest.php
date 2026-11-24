<?php

namespace Tests\Feature;

use App\Models\AnalysisResult;
use App\Models\Dataset;
use App\Models\DatasetColumn;
use App\Models\DatasetRow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_dataset_upload_stores_file_in_local_private_disk()
    {
        Storage::fake('local');
        Storage::fake('public');

        $user = User::factory()->create();

        $csvContent = "Name,Score\nAlice,85\nBob,90\nCharlie,95\n";
        $file = UploadedFile::fake()->createWithContent('test_scores.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('datasets.store'), [
            'name' => 'Test Dataset',
            'file' => $file,
        ]);

        $response->assertRedirect();

        $dataset = Dataset::first();
        $this->assertNotNull($dataset);
        $this->assertEquals('Test Dataset', $dataset->name);

        // Verify file stored in 'local' disk, NOT 'public'
        Storage::disk('local')->assertExists($dataset->file_path);
        Storage::disk('public')->assertMissing($dataset->file_path);
    }

    public function test_update_cell_rejects_unwhitelisted_column_injection()
    {
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Test Dataset', 'file_path' => 'datasets/test.csv']);

        DatasetColumn::create([
            'dataset_id' => $dataset->id,
            'column_name' => 'age',
            'column_index' => 0,
            'data_type' => 'numeric',
        ]);

        $row = DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => 1,
            'data' => ['age' => 25],
        ]);

        // Attempting to update a column not in dataset_columns
        $response = $this->actingAs($user)->postJson(route('datasets.update_cell', $dataset), [
            'row_id' => $row->id,
            'column_name' => '__injected_column_or_sql',
            'value' => 'malicious',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Kolom tidak valid.']);

        // Legitimate column update succeeds
        $validResponse = $this->actingAs($user)->postJson(route('datasets.update_cell', $dataset), [
            'row_id' => $row->id,
            'column_name' => 'age',
            'value' => '30',
        ]);

        $validResponse->assertOk();
        $this->assertEquals('30', $row->fresh()->data['age']);
    }

    public function test_csv_export_sanitizes_formula_injection()
    {
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Formula Test', 'file_path' => 'datasets/test.csv']);

        DatasetColumn::create([
            'dataset_id' => $dataset->id,
            'column_name' => 'payload',
            'column_index' => 0,
            'data_type' => 'categorical',
        ]);

        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => 1,
            'data' => ['payload' => '=cmd|"/C calc"!A0'],
        ]);

        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => 2,
            'data' => ['payload' => '@SUM(1+1)'],
        ]);

        $response = $this->actingAs($user)->get(route('export.dataset_csv', $dataset));
        $response->assertOk();

        $content = $response->streamedContent();

        // Formula triggers (=, @, +, -) must be escaped with a leading single quote
        $this->assertStringContainsString("'=cmd", $content);
        $this->assertStringContainsString("'@SUM", $content);
    }

    public function test_analysis_descriptive_reads_from_database_rows_reflecting_edits()
    {
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Analysis Test', 'file_path' => 'datasets/test.csv']);

        DatasetColumn::create([
            'dataset_id' => $dataset->id,
            'column_name' => 'nilai',
            'column_index' => 0,
            'data_type' => 'numeric',
        ]);

        // Insert database rows
        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => 1,
            'data' => ['nilai' => 10],
        ]);
        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => 2,
            'data' => ['nilai' => 20],
        ]);
        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => 3,
            'data' => ['nilai' => 30],
        ]);

        $response = $this->actingAs($user)->post(route('analysis.descriptive', $dataset), [
            'column' => 'nilai',
        ]);

        $response->assertOk();
        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');

        $this->assertEquals(3, $stats['n']);
        $this->assertEquals(20.0, (float) $stats['mean']);
        $this->assertEquals(10.0, (float) $stats['min']);
        $this->assertEquals(30.0, (float) $stats['max']);
    }

    public function test_analysis_correlation_and_regression_reads_from_database_rows()
    {
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Regression Test', 'file_path' => 'datasets/test.csv']);

        DatasetColumn::create(['dataset_id' => $dataset->id, 'column_name' => 'x', 'column_index' => 0, 'data_type' => 'numeric']);
        DatasetColumn::create(['dataset_id' => $dataset->id, 'column_name' => 'y', 'column_index' => 1, 'data_type' => 'numeric']);

        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 1, 'data' => ['x' => 1, 'y' => 2]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 2, 'data' => ['x' => 2, 'y' => 4]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 3, 'data' => ['x' => 3, 'y' => 6]]);

        // Test correlation
        $corrResp = $this->actingAs($user)->post(route('analysis.correlation', $dataset), [
            'column_x' => 'x',
            'column_y' => 'y',
            'method' => 'pearson',
        ]);
        $corrResp->assertOk();
        $corrResult = $corrResp->viewData('result');
        $this->assertEquals(1.0, (float) $corrResult['r']);

        // Test simple regression
        $regResp = $this->actingAs($user)->post(route('analysis.regression_simple', $dataset), [
            'column_x' => 'x',
            'column_y' => 'y',
        ]);
        $regResp->assertOk();
        $regResult = $regResp->viewData('result');
        $this->assertEquals(2.0, (float) $regResult['b1']);
    }

    public function test_analysis_categorical_and_anova_reads_from_database_rows()
    {
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Categorical Test', 'file_path' => 'datasets/test.csv']);

        DatasetColumn::create(['dataset_id' => $dataset->id, 'column_name' => 'gender', 'column_index' => 0, 'data_type' => 'categorical']);
        DatasetColumn::create(['dataset_id' => $dataset->id, 'column_name' => 'score', 'column_index' => 1, 'data_type' => 'numeric']);

        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 1, 'data' => ['gender' => 'Male', 'score' => 70]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 2, 'data' => ['gender' => 'Male', 'score' => 80]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 3, 'data' => ['gender' => 'Female', 'score' => 85]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 4, 'data' => ['gender' => 'Female', 'score' => 95]]);

        // Test categorical frequency
        $catResp = $this->actingAs($user)->post(route('analysis.categorical', $dataset), [
            'column' => 'gender',
        ]);
        $catResp->assertOk();
        $frequencies = $catResp->viewData('frequencies');
        $this->assertEquals(2, $frequencies['Male']);
        $this->assertEquals(2, $frequencies['Female']);

        // Test independent t-test
        $ttestResp = $this->actingAs($user)->post(route('analysis.ttest_independent', $dataset), [
            'column_value' => 'score',
            'column_group' => 'gender',
        ]);
        $ttestResp->assertOk();
        $ttestResult = $ttestResp->viewData('result');
        $this->assertEquals(75.0, (float) $ttestResult['mean1']);
        $this->assertEquals(90.0, (float) $ttestResult['mean2']);
    }

    public function test_unauthorized_user_cannot_access_or_modify_other_users_dataset()
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $dataset = Dataset::create(['user_id' => $owner->id, 'name' => 'Owner Dataset', 'file_path' => 'datasets/test.csv']);

        // Attacker trying to view
        $response = $this->actingAs($attacker)->get(route('datasets.show', $dataset));
        $response->assertStatus(403);

        // Attacker trying to delete
        $response = $this->actingAs($attacker)->delete(route('datasets.destroy', $dataset));
        $response->assertStatus(403);

        // Attacker trying to run analysis
        $response = $this->actingAs($attacker)->get(route('analysis.create', $dataset));
        $response->assertStatus(403);
    }
}

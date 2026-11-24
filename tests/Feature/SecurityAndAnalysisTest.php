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

    public function test_zero_variance_inputs_are_safely_handled_without_division_by_zero_crash()
    {
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Constant Data', 'file_path' => 'datasets/test.csv']);

        DatasetColumn::create(['dataset_id' => $dataset->id, 'column_name' => 'constant_col', 'column_index' => 0, 'data_type' => 'numeric']);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 1, 'data' => ['constant_col' => 10]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 2, 'data' => ['constant_col' => 10]]);
        DatasetRow::create(['dataset_id' => $dataset->id, 'row_number' => 3, 'data' => ['constant_col' => 10]]);

        // t-test one sample on constant column must return validation error instead of throwing DivisionByZeroError (500)
        $resp = $this->actingAs($user)->post(route('analysis.ttest_one_sample', $dataset), [
            'column' => 'constant_col',
            'test_value' => 10,
        ]);
        $resp->assertRedirect();
        $resp->assertSessionHasErrors(['column']);
    }

    public function test_dataset_destroy_deletes_associated_chart_files_on_disk()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $dataset = Dataset::create(['user_id' => $user->id, 'name' => 'Chart Dataset', 'file_path' => 'datasets/chart_test.csv']);

        Storage::disk('local')->put($dataset->file_path, 'mock csv content');

        $analysis = AnalysisResult::create([
            'dataset_id' => $dataset->id,
            'user_id' => $user->id,
            'analysis_type' => 'descriptive',
            'analysis_label' => 'Test Chart',
            'parameters' => [],
            'result' => [],
            'chart_path' => 'charts/9999.png',
        ]);
        Storage::disk('local')->put('charts/9999.png', 'fake png content');

        Storage::disk('local')->assertExists('charts/9999.png');

        $this->actingAs($user)->delete(route('datasets.destroy', $dataset));

        // Both the raw dataset file and the generated chart image must be deleted from disk
        Storage::disk('local')->assertMissing('datasets/chart_test.csv');
        Storage::disk('local')->assertMissing('charts/9999.png');
    }

    public function test_prune_guests_command_removes_old_guests_and_files()
    {
        Storage::fake('local');

        // Create an expired guest (>48h ago)
        $oldGuest = User::create([
            'name' => 'Guest-OLD',
            'email' => 'old_guest@statweb.local',
            'password' => 'secret',
            'is_guest' => true,
            'guest_token' => 'oldtoken12345678901234567890123456789012',
        ]);
        User::where('id', $oldGuest->id)->update(['created_at' => now()->subHours(72)]);

        $oldDataset = Dataset::create([
            'user_id' => $oldGuest->id,
            'name' => 'Old Dataset',
            'file_path' => 'datasets/old.csv',
        ]);
        Storage::disk('local')->put('datasets/old.csv', 'old data');

        // Create an active guest (1h ago)
        $newGuest = User::create([
            'name' => 'Guest-NEW',
            'email' => 'new_guest@statweb.local',
            'password' => 'secret',
            'is_guest' => true,
            'guest_token' => 'newtoken12345678901234567890123456789012',
            'created_at' => now()->subHour(),
        ]);

        $this->artisan('statweb:prune-guests --hours=48')
            ->assertExitCode(0);

        // Old guest and its file should be deleted
        $this->assertDatabaseMissing('users', ['id' => $oldGuest->id]);
        Storage::disk('local')->assertMissing('datasets/old.csv');

        // New guest should still exist
        $this->assertDatabaseHas('users', ['id' => $newGuest->id]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dataset_columns', function (Blueprint $table) {
            $table->string('label')->nullable()->after('column_name');
            $table->integer('decimals')->default(2)->after('data_type');
            $table->enum('measure', ['nominal', 'ordinal', 'scale'])->default('nominal')->after('decimals');
        });
    }

    public function down(): void
    {
        Schema::table('dataset_columns', function (Blueprint $table) {
            $table->dropColumn(['label', 'decimals', 'measure']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_picas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->text('problem_identification');
            $table->text('root_cause_analysis');
            $table->text('corrective_action');
            $table->text('preventive_action');
            $table->string('pic_name')->default('QA & Warehouse Lead');
            $table->date('target_completion_date')->nullable();
            $table->string('status')->default('PENDING_AUDIT');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_picas');
    }
};

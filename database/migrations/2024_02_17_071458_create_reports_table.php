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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('patient_name');
            $table->string('file_number');
            $table->integer('age');
            $table->text('relevant_history')->nullable();
            $table->string('approval')->default('pending');
            $table->text('image_path')->nullable();
            $table->longText('report')->nullable();
            $table->string('insurance')->nullable();
            $table->string('other_insurance_type')->nullable();
            $table->string('xray_type')->nullable();
            $table->string('mri_type')->nullable();
            $table->string('ct_scan_type')->nullable();
            $table->string('ultrasound_type')->nullable();
            $table->string('report_type');
            $table->string('comment')->nullable();
            $table->boolean('forwarded')->default(false);
            $table->tinyInteger('auto_assign')->nullable();
            $table->foreign('hospital_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_ray_reports');
    }
};

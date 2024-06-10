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
        Schema::create('radiologist_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
//            $table->string('name');
            $table->string('age');
            $table->string('mmed_graduation_year');
            $table->string('mmed_completed_from');
            $table->integer('experience');
            $table->string('mct_number');
            $table->string('mmed_certificate');
            $table->string('mct_license');
            $table->string('cv');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiologist_profiles');
    }
};

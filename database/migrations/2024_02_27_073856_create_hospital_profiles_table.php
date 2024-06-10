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
        Schema::create('hospital_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
//            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('region');
            $table->string('authorized_rep_name');

            $table->string('authorized_rep_phone');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_profiles');
    }
};

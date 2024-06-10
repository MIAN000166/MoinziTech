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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('username')->nullable();
            $table->string('name')->nullable();
//            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->string('slug');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->boolean('user_verified')->default(false);
            $table->string('status')->default('inactive');
            $table->string('email_otp')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('str_name', 255);
            $table->string('str_email', 255)->unique();
            $table->string('str_phone', 20)->nullable();
            $table->string('str_profile_picture_path', 255)->nullable();
            $table->index('str_name');
            $table->index('str_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

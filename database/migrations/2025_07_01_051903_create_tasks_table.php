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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();   // Auto-incrementing ID (e.g., 1, 2, 3)
            $table->string('name');  // Task name (text)
            $table->boolean('completed')->default(false);   // true/false value (initially false)
            $table->timestamps();   // Two fields: created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
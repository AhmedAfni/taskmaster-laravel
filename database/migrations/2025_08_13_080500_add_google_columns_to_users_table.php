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
    // Add columns only if they do not already exist
    if (
        !Schema::hasColumn('users', 'google_access_token') ||
        !Schema::hasColumn('users', 'google_refresh_token') ||
        !Schema::hasColumn('users', 'google_token_expires')
    ) {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_access_token')) {
                $table->text('google_access_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'google_refresh_token')) {
                $table->text('google_refresh_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'google_token_expires')) {
                $table->timestamp('google_token_expires')->nullable();
            }
        });
    }
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $columnsToDrop = [];
        if (Schema::hasColumn('users', 'google_access_token')) {
            $columnsToDrop[] = 'google_access_token';
        }
        if (Schema::hasColumn('users', 'google_refresh_token')) {
            $columnsToDrop[] = 'google_refresh_token';
        }
        if (Schema::hasColumn('users', 'google_token_expires')) {
            $columnsToDrop[] = 'google_token_expires';
        }
        if (!empty($columnsToDrop)) {
            $table->dropColumn($columnsToDrop);
        }
    });
}

};

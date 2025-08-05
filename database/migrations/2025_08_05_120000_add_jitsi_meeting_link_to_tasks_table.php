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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('jitsi_meeting_link')->nullable()->after('google_meet_link');
            $table->dateTime('jitsi_scheduled_at')->nullable()->after('jitsi_meeting_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['jitsi_meeting_link', 'jitsi_scheduled_at']);
        });
    }
};

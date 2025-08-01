<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->text('google_access_token')->nullable();
        $table->text('google_refresh_token')->nullable();
        $table->timestamp('google_token_expires')->nullable();
    });
}
public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['google_access_token', 'google_refresh_token', 'google_token_expires']);
    });
}
};

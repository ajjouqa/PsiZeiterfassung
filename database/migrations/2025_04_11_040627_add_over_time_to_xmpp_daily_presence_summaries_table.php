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
        Schema::table('xmpp_daily_presence_summaries', function (Blueprint $table) {
            //
            // Add the new column for over_time
            $table->integer('over_time')->default(0)->after('total_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xmpp_daily_presence_summaries', function (Blueprint $table) {
            //
        });
    }
};

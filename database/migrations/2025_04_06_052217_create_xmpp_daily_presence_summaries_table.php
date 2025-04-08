<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXmppDailyPresenceSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xmpp_daily_presence_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('user_type');
            $table->unsignedBigInteger('user_id');
            $table->string('xmpp_username');
            $table->date('date');
            $table->integer('total_seconds')->default(0);
            $table->string('formatted_time')->nullable();
            $table->integer('session_count')->default(0);
            $table->timestamp('first_login')->nullable();
            $table->timestamp('last_logout')->nullable();
            $table->timestamps();
            
            // Create a unique constraint to ensure one record per user per day
            $table->unique(['user_type', 'user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xmpp_daily_presence_summaries');
    }
}
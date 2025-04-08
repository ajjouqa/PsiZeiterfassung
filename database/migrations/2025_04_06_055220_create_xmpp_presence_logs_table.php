<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXmppPresenceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xmpp_presence_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type');
            $table->unsignedBigInteger('user_id');
            $table->string('xmpp_username');
            $table->string('event_type');  // login, logout, status_change
            $table->string('presence');    // available, away, chat, dnd, xa, unavailable
            $table->string('status')->nullable();
            $table->timestamp('timestamp');
            $table->string('resource')->nullable();
            $table->string('ip_address')->nullable();
            
            $table->index(['user_type', 'user_id']);
            $table->index('xmpp_username');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xmpp_presence_logs');
    }
}
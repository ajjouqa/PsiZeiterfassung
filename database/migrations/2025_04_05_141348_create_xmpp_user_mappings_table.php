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
        Schema::create('xmpp_user_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('xmpp_username')->unique();
            $table->string('user_type')->comment('azubi, admin, mitarbeiter');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xmpp_user_mappings');
    }
};

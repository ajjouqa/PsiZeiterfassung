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
        Schema::table('xmpp_user_mappings', function (Blueprint $table) {
            $table->string('current_presence')->default('unavailable')->after('user_id');
            $table->string('current_status')->nullable()->after('current_presence');
            $table->timestamp('last_logout')->nullable()->after('current_status');
            $table->timestamp('presence_updated_at')->nullable()->after('last_logout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xmpp_user_mappings', function (Blueprint $table) {
            //
        });
    }
};

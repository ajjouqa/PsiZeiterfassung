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
            //
            $table->boolean('is_active')->default(true)->after('presence_updated_at');
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

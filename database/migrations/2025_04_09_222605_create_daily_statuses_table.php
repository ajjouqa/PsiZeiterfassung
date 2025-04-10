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
        Schema::create('daily_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_summary_id')
                  ->constrained('xmpp_daily_presence_summaries')
                  ->onDelete('cascade');

            $table->enum('status', ['working', 'school', 'sick', 'off'])->default('working');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_statuses');
    }
};

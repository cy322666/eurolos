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
        Schema::create('hooks_talks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('talk_id');
            $table->dateTime('talk_created_at');
            $table->string('rate');
            $table->integer('contact_id');
            $table->string('chat_id');
            $table->integer('entity_id');
            $table->string('entity_type');
            $table->string('is_in_work');
            $table->string('is_read');
            $table->string('origin');
            $table->json('body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hooks_talks');
    }
};

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
        Schema::create('events_calls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('event_id')->unique();
            $table->string('type');
            $table->integer('entity_id');
            $table->integer('entity_type');
            $table->integer('created_by');
            $table->dateTime('call_created_at');
            $table->integer('call_created_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};

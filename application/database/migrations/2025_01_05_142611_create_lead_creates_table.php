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
        Schema::create('events_lead_create', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('event_id')->unique();
            $table->integer('entity_id');
            $table->integer('event_created_by');
            $table->dateTime('event_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_creates');
    }
};

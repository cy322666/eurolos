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
        Schema::table('hooks_talks', function (Blueprint $table) {

            $table->integer('responsible_contact')->nullable();
            $table->integer('responsible_lead')->nullable();
            $table->integer('status_id')->nullable();
        });

        Schema::table('events_lead_status', function (Blueprint $table) {

            $table->integer('responsible_lead')->nullable();
            $table->string('category')->nullable();
            $table->string('loss_reason')->nullable();
            $table->string('company_source')->nullable();
            $table->string('channel_source')->nullable();
        });

        Schema::table('events_lead_create', function (Blueprint $table) {

            $table->integer('responsible_contact')->nullable();
            $table->integer('responsible_lead')->nullable();
            $table->string('source')->nullable();
            $table->string('category')->nullable();
        });

        Schema::table('events_calls', function (Blueprint $table) {

            $table->integer('responsible_contact')->nullable();
            $table->integer('responsible_lead')->nullable();
            $table->integer('status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};

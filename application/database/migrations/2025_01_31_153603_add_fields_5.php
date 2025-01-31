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

            $table->string('responsible_name')->nullable();
            $table->string('status_name')->nullable();
            $table->string('lead_created_date')->nullable();
            $table->string('lead_created_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('lead_id')->unique();
            $table->integer('contact_id')->nullable();
            $table->integer('responsible_lead')->nullable();
            $table->string('category')->nullable();
            $table->string('loss_reason')->nullable();
            $table->string('company_source')->nullable();
            $table->string('channel_source')->nullable();
            $table->string('returned_failure')->nullable();
            $table->string('lead_class')->nullable();
            $table->string('measured')->nullable();
            $table->date('date_measured')->nullable();
            $table->date('lead_created_date')->nullable();
            $table->time('lead_created_time')->nullable();
            $table->date('date_sale_op')->nullable();
            $table->date('date_install')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

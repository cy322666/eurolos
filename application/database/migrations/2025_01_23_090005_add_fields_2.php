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
        Schema::table('events_lead_status', function (Blueprint $table) {

            $table->string('returned_failure')->nullable();
            $table->string('lead_class')->nullable();
            $table->string('measured')->nullable();
            $table->date('date_measured')->nullable();
            $table->date('event_created_date')->nullable();
            $table->time('event_created_time')->nullable();
            $table->date('date_sale_op')->nullable();
            $table->date('date_install')->nullable();

//            $table->dropColumn('date_measured')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_lead_status', function (Blueprint $table) {

            $table->dropColumn('returned_failure')->nullable();
            $table->dropColumn('lead_class')->nullable();
            $table->dropColumn('measured')->nullable();
            $table->dropColumn('event_created_date')->nullable();
            $table->dropColumn('event_created_time')->nullable();
            $table->dropColumn('date_measured')->nullable();
            $table->dropColumn('date_sale_op')->nullable();
            $table->dropColumn('date_install')->nullable();

//            $table->dateTime('date_measured')->nullable();
        });
    }
};

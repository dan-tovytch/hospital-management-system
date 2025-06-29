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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("nurses_id")->references("id")->on("nurses")->onDelete('restrict');
            $table->tinyInteger("days_week");
            $table->time("start_time");
            $table->time("end_time");
            $table->timestamps();
            $table->boolean('active')->default(true);
        });

        Schema::create("queries", function (Blueprint $table) {
            $table->id();
            $table->foreignId("nurses_id")->references("id")->on("nurses")->onDelete('restrict');
            $table->foreignId("patients_id")->references("id")->on("patients")->onDelete('restrict');
            $table->dateTime("date");
            $table->integer("query_type");
            $table->timestamps();
        });

        Schema::create("medical_records", function (Blueprint $table) {
            $table->id();
            $table->foreignId("querie_id")->references("id")->on("queries")->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
        Schema::dropIfExists('queries');
        Schema::dropIfExists('medical_records');
    }
};

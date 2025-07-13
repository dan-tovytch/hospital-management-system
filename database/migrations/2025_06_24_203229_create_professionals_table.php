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
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string("name", 255);
            $table->timestamps();
        });

        Schema::create('nurses', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable()->index()->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string("first_name", 255);
            $table->string("last_name", 255);
            $table->foreignId("specialtie_id")->references("id")->on("specialties")->onDelete("restrict");
            $table->text('cpf');
            $table->string('cpf_hash', 64)->unique()->index();
            $table->foreignId("address_id")->nullable()->references("id")->on("address")->onDelete('restrict');
            $table->string("coren")->unique()->index();
            $table->string("phone_number")->unique()->index();
            $table->date("date_birth");
            $table->timestamps();
            $table->date("termination_date")->nullable();
            $table->boolean('active')->default(true);
        });

        Schema::create('administrators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->index()->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string("first_name", 255);
            $table->string("last_name", 255);
            $table->text('cpf');
            $table->string('cpf_hash', 64)->unique()->index();
            $table->foreignId("address_id")->references("id")->on("address")->onDelete('restrict');
            $table->timestamps();
            $table->date("termination_date")->nullable();
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialties');
        Schema::dropIfExists('nurses');
        Schema::dropIfExists('administrators');
    }
};

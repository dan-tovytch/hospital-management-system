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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable()->index()->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string("first_name", 255);
            $table->string("last_name", 255);
            $table->text('cpf');
            $table->string('cpf_hash', 64)->unique()->index();
            $table->foreignId("address_id")->nullable()->references("id")->on("address")->onDelete('restrict');
            $table->string("phone_number")->unique()->index();
            $table->date("date_birth");
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

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
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable()->index()->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('street', 255);
            $table->integer('number');
            $table->string('city', 255);
            $table->string('neighborhood', 255);
            $table->string('state', 255);
            $table->string('cep', 9);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};

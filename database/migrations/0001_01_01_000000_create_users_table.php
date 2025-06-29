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

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique()->index();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('restrict');
            $table->boolean('active')->default(true);
            $table->dateTime('last_login')->nullable();
            $table->integer('login_attempts')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->timestamp('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

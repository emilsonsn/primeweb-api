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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('company');
            $table->string('domain');
            $table->string('responsible');
            $table->string('origin');
            $table->date('return_date');
            $table->string('cnpj');
            $table->string('cep')->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('contact_phones', function (Blueprint $table) {
            $table->string('phone');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('contact_emails', function (Blueprint $table) {
            $table->string('email');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('contact_segments', function (Blueprint $table) {
            $table->string('segment');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_phones');
        Schema::dropIfExists('contact_emails');
        Schema::dropIfExists('contact_segments');
    }
};

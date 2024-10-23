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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('domain');
            $table->string('cnpj');
            $table->string('client_responsable_name')->nullable();
            $table->string('client_responsable_name_2')->nullable();
            $table->string('cep');
            $table->string('street');
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state');

            $table->decimal('monthly_fee');
            $table->date('payment_first_date');
            $table->integer('duedate_day');
            $table->date('final_date');

            $table->text('observations');

            $table->unsignedBigInteger('segment_id');
            $table->unsignedBigInteger('consultant_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('technical_id');
            $table->timestamps();
            
            $table->foreign('segment_id')->references('id')->on('segments');
            $table->foreign('consultant_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('technical_id')->references('id')->on('users');
        });

        Schema::create('client_emails', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('client_phones', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('client_contracts', function(Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('path');
            $table->date('date_hire');
            $table->integer('number_words_contract');
            $table->enum('service_type', ['PLAN_A', 'PLAN_B_SILVER', 'PLAN_B_GOLD']);
            $table->enum('model', [
                'V1', 'V2', 'V3', 'V4', 'V5',
                'CLIENT_LAYOUT',
                'CUSTOMIZED',
                'N1', 'N2', 'N3'
            ]);
            $table->text('observations');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_emails');
        Schema::dropIfExists('client_phones');
        Schema::dropIfExists('client_contracts');
        Schema::dropIfExists('clients');
    }
};
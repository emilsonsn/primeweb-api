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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('company')->change();
            $table->string('domain')->change();
            $table->string('cnpj')->nullable()->change();
            $table->string('client_responsable_name')->change();
            $table->string('client_responsable_name_2')->nullable()->change();
            $table->string('cep')->change();
            $table->string('street')->nullable()->change();
            $table->string('neighborhood')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->decimal('monthly_fee')->change();
            $table->date('payment_first_date')->nullable()->change();
            $table->integer('duedate_day')->nullable()->change();
            $table->date('final_date')->nullable()->change();
            $table->text('observations')->nullable()->change();
            $table->unsignedBigInteger('segment_id')->change();
            $table->unsignedBigInteger('consultant_id')->change();
            $table->unsignedBigInteger('seller_id')->change();
            $table->unsignedBigInteger('technical_id')->change();
        });

        Schema::table('client_emails', function(Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->change();
            $table->unsignedBigInteger('client_id')->change();
        });

        Schema::table('client_phones', function(Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('phone')->change();
            $table->unsignedBigInteger('client_id')->change();
        });

        Schema::table('client_contracts', function(Blueprint $table) {
            $table->string('number')->change();
            $table->string('path')->change();
            $table->date('date_hire')->change();
            $table->integer('number_words_contract')->change();
            $table->enum('service_type', ['PLAN_A', 'PLAN_B_SILVER', 'PLAN_B_GOLD'])->change();
            $table->string('model')->nullable()->change();
            $table->text('observations')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
};

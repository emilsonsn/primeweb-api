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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cep')->after('phone')->nullable();
            $table->string('street')->after('cep')->nullable();
            $table->string('neighborhood')->after('street')->nullable();
            $table->string('city')->after('neighborhood')->nullable();
            $table->string('state')->after('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cep');
            $table->dropColumn('street');
            $table->dropColumn('neighborhood');
            $table->dropColumn('city');
            $table->dropColumn('state');
        });
    }
};

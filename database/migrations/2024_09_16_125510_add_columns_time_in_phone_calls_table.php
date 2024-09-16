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
        Schema::table('phone_calls', function (Blueprint $table) {
            $table->time('return_time')->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->time('return_time')->after('return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phone_calls', function (Blueprint $table) {
            $table->date('return_time')->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('return_time');
        });
    }
};

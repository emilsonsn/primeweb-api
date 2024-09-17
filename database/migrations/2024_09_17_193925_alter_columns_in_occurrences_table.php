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
        Schema::table('occurrences', function (Blueprint $table) {
            $table->unsignedBigInteger('phone_call_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('contact_id')->nullable()->after('phone_call_id');

            $table->foreign('phone_call_id')->references('id')->on('phone_calls');
            $table->foreign('contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occurrences', function (Blueprint $table) {
            $table->dropForeign('occurrences_phone_call_id_foreign');
            $table->dropForeign('occurrences_contact_id_foreign');
            
            $table->dropColumn(['phone_call_id', 'contact_id']);
        });
    }
};

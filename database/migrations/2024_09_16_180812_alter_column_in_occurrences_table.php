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
            $table->enum('status', ['Lead', 'PresentationVisit', 'ConvertedContact', 'SchedulingVisit', 'ReschedulingVisit', 'DelegationContact', 'InNegotiation', 'Closed', 'Lost'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occurrences', function (Blueprint $table) {
            $table->enum('status', ['Lead', 'PresentationVisit', 'SchedulingVisit', 'ReschedulingVisit', 'DelegationContact', 'InNegotiation', 'Closed', 'Lost'])->change();
        });
    }
};

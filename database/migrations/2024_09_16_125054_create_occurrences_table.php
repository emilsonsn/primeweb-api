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
        Schema::create('occurrences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['Lead', 'PresentationVisit', 'SchedulingVisit', 'ReschedulingVisit', 'DelegationContact', 'InNegotiation', 'Closed', 'Lost']);
            $table->string('link')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occurrences');
    }
};

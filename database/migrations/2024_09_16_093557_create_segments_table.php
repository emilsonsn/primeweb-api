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
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('contact_segments', function (Blueprint $table) {
            $table->dropColumn('segment');
            $table->unsignedBigInteger('segment_id');

            $table->foreign('segment_id')->references('id')->on('segments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_segments', function (Blueprint $table) {
            $table->dropForeign(['segment_id']);
            $table->dropColumn(['segment_id']);

            $table->string('segment');
        });

        Schema::dropIfExists('segments');

    }
};

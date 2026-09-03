<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->integer('pax');
            $table->timestamp('started_at')->nullable(); // Renamed from logged_at or mapped
            $table->timestamp('ended_at')->nullable();   // Added session end time
            $table->string('time_elapsed')->nullable();  // Automatically calculated duration (e.g., "1h 15m")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number')->unique();
            $table->enum('status', ['available', 'occupied', 'dirty'])->default('available');
            $table->integer('capacity')->default(4);
            $table->integer('pax')->nullable();
            $table->timestamp('seated_time')->nullable();
            $table->timestamp('cleared_time')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
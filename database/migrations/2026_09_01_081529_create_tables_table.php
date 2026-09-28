<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('table_number')->nullable(); // Remove ->unique(), add ->nullable()
            $table->enum('status', ['available', 'occupied', 'dirty'])->default('available');
            $table->integer('capacity')->nullable()->default(0); // Allow empty pax default
            $table->integer('pax')->nullable();

            // 2D Layout Grid Properties
            $table->string('shape')->default('square'); // square, rectangle, circle
            $table->integer('grid_x')->default(1);
            $table->integer('grid_y')->default(1);
            $table->integer('width')->default(2);  // Grid spans (e.g., 2x2 cells)
            $table->integer('height')->default(2);

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

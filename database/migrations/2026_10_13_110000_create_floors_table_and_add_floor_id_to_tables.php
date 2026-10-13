<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create floors table
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('name'); // e.g., "Lantai 1", "Lantai 2", "Rooftop"
            $table->integer('floor_number')->default(1);
            $table->integer('grid_width')->default(20);
            $table->integer('grid_height')->default(20);
            $table->longText('grid_sections')->nullable();
            $table->longText('section_templates')->nullable();
            $table->timestamps();

            $table->index(['outlet_id', 'floor_number']);
        });

        // 2. Add floor_id to tables table
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('floor_id')->nullable()->after('outlet_id')->constrained('floors')->nullOnDelete();
        });

        // 3. Migrate existing outlets and tables data
        $outlets = DB::table('outlets')->get();
        foreach ($outlets as $outlet) {
            $gridWidth = DB::table('settings')->where('outlet_id', $outlet->id)->where('key', 'grid_width')->value('value') ?? 20;
            $gridHeight = DB::table('settings')->where('outlet_id', $outlet->id)->where('key', 'grid_height')->value('value') ?? 20;
            $gridSections = DB::table('settings')->where('outlet_id', $outlet->id)->where('key', 'grid_sections')->value('value') ?? '[]';
            $sectionTemplates = DB::table('settings')->where('outlet_id', $outlet->id)->where('key', 'section_templates')->value('value') ?? '[]';

            $floorId = DB::table('floors')->insertGetId([
                'outlet_id'         => $outlet->id,
                'name'              => 'Lantai 1',
                'floor_number'      => 1,
                'grid_width'        => (int) $gridWidth ?: 20,
                'grid_height'       => (int) $gridHeight ?: 20,
                'grid_sections'     => $gridSections,
                'section_templates' => $sectionTemplates,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Assign all existing tables of this outlet to this default floor
            DB::table('tables')->where('outlet_id', $outlet->id)->update([
                'floor_id' => $floorId
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['floor_id']);
            $table->dropColumn('floor_id');
        });

        Schema::dropIfExists('floors');
    }
};
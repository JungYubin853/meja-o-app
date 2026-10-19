<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'viewable_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('viewable_password')->nullable()->after('password');
            });
        }

        if (!Schema::hasTable('password_reset_requests')) {
            Schema::create('password_reset_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('email');
                $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');
                $table->string('role');
                $table->string('status')->default('pending'); // 'pending', 'resolved'
                $table->text('admin_notes')->nullable();
                $table->string('resolved_by')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        }

        // Backfill default passwords for existing users so admin/super admin can see them
        try {
            $users = User::whereNull('viewable_password')->get();
            foreach ($users as $user) {
                $defaultPass = 'staff123';
                if ($user->role === 'super_admin') {
                    $defaultPass = 'sadmin123';
                } elseif ($user->role === 'admin') {
                    $defaultPass = 'admin123';
                }
                $user->viewable_password = Crypt::encryptString($defaultPass);
                $user->save();
            }
        } catch (\Throwable $e) {
            // Log or ignore if key not set during test
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');

        if (Schema::hasColumn('users', 'viewable_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('viewable_password');
            });
        }
    }
};

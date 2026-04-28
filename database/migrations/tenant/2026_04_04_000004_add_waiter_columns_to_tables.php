<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('assigned_waiter_id')->nullable()->constrained('users')->nullOnDelete()->after('occupied');
            $table->timestamp('assigned_at')->nullable()->after('assigned_waiter_id');
            $table->timestamp('greeted_at')->nullable()->after('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_waiter_id');
            $table->dropColumn(['assigned_at', 'greeted_at']);
        });
    }
};

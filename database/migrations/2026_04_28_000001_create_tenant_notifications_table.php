<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->text('message');
            $table->string('sent_by')->default('SuperAdmin');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_notifications');
    }
};

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
        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('STANDBY'); // ACTIVE, STANDBY, ALL_CLEAR
            $table->string('type')->default('REAL'); // REAL, DRILL
            $table->string('severity')->default('CRITICAL'); // CRITICAL, WARNING, INFO
            $table->string('title')->default('AMARAN PENGUNGSIAN KECEMASAN!');
            $table->text('message');
            $table->string('triggered_by')->default('Admin'); // Admin, Quick PIN, System
            $table->timestamp('triggered_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('assembly_point_id')->nullable()->constrained('assembly_points')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_alerts');
    }
};

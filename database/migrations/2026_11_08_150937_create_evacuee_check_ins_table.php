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
        Schema::create('evacuee_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_alert_id')->nullable()->constrained('emergency_alerts')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('matric_no')->nullable();
            $table->string('status')->default('SAFE'); // SAFE, INJURED, TRAPPED
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('device_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evacuee_check_ins');
    }
};

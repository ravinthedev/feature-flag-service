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
        if (Schema::hasTable('car_reports')) {
            return;
        }
        
        Schema::create('car_reports', function (Blueprint $table) {
            $table->id();
            $table->string('car_model');
            $table->text('description');
            $table->string('damage_type'); // minor, moderate, severe, total_loss
            $table->string('photo_url')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, rejected
            $table->timestamps();

            $table->index(['status']);
            $table->index(['damage_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_reports');
    }
};

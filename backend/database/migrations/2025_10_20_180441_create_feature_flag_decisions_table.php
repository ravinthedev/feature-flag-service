<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_flag_decisions', function (Blueprint $table) {
            $table->id();
            $table->string('flag_key');
            $table->boolean('enabled');
            $table->string('reason');
            $table->json('context')->nullable();
            $table->string('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('evaluated_at');
            $table->timestamps();

            $table->index(['flag_key', 'evaluated_at']);
            $table->index(['user_id', 'evaluated_at']);
            $table->index(['enabled', 'evaluated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flag_decisions');
    }
};
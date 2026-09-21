<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained()->cascadeOnDelete();
            $table->string('field');
            $table->string('operator');
            $table->string('value');
            $table->string('logic_operator')->default('AND');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_conditions');
    }
};

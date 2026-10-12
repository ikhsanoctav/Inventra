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
        Schema::create('damaged_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->default(1);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->integer('quantity');
            $table->string('damage_type'); // Rusak Fisik, Kadaluarsa / Expired, Cacat Pabrik, Hilang / Selisih
            $table->string('action_type'); // Karantina, Pemusnahan / Write-Off, Retur Supplier
            $table->string('status')->default('Tercatat'); // Tercatat, Dalam Karantina, Dimusnahkan, Diretur
            $table->text('notes')->nullable();
            $table->dateTime('reported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_items');
    }
};

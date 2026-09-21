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
        Schema::table('items', function (Blueprint $table) {
            $table->string('type')->default('goods')->after('sku'); // goods or service
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('status');
            $table->string('payment_method')->nullable()->after('total_amount'); // Cash, Transfer, etc.
            $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'payment_method', 'paid_amount']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

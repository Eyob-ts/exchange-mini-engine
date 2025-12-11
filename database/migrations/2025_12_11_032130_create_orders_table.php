<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 10);
            $table->string('side', 10);                    
            $table->decimal('price', 24, 8);
            $table->decimal('amount', 32, 16);
            $table->decimal('locked_usd', 24, 8)->nullable();
            $table->tinyInteger('status')->default(1);     
            $table->timestamps();
            $table->index(['symbol', 'status']);
            $table->index('user_id');
            $table->index(['side', 'price']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

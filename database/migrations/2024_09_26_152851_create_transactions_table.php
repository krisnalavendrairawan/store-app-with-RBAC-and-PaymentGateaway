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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // relasi ke user
            $table->foreignId('product_id')->constrained('product')->onDelete('cascade'); // relasi ke produk
            $table->decimal('amount', 15, 2); // jumlah total pembayaran
            $table->string('status');
            $table->string('payment_type')->default('qris'); // tipe pembayaran (di-set 'qris' untuk QR saja)
            $table->string('order_id')->unique(); // ID pesanan unik dari Midtrans
            $table->string('transaction_id')->nullable(); // ID transaksi dari Midtrans
            $table->string('qris_url')->nullable(); // URL QR code untuk pembayaran QRIS
            $table->timestamp('transaction_time')->nullable(); // waktu transaksi dari Midtrans
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

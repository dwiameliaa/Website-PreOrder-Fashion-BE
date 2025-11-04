<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_measurements', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->enum('product_type', ['shirt', 'pants']);
            $table->enum('measurement_type', ['standard', 'custom']);
            $table->enum('size', ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'])->nullable();
            $table->decimal('panjang_bahu', 5, 2)->nullable();
            $table->decimal('panjang_lengan', 5, 2)->nullable();
            $table->decimal('lingkar_dada', 5, 2)->nullable();
            $table->decimal('panjang_baju', 5, 2)->nullable();
            $table->decimal('lingkar_pinggang', 5, 2)->nullable();
            $table->decimal('panjang_celana', 5, 2)->nullable();
            $table->decimal('lingkar_paha', 5, 2)->nullable();
            $table->decimal('lingkar_betis', 5, 2)->nullable();
            $table->decimal('lingkar_lutut', 5, 2)->nullable();
            $table->decimal('lingkar_kaki', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['requested', 'in_progress', 'done', 'cancelled'])->default('requested');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_measurements');
    }
};

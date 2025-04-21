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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('category_id');
            $table->string('thumbnail');
            $table->string('short_desc')->nullable();
            $table->double('price')->default(0);
            $table->integer('stock')->default(0);
            $table->text('desc');
            $table->unsignedBigInteger('total_view')->default(0);
            $table->unsignedBigInteger('total_share')->default(0);
            $table->unsignedBigInteger('total_react')->default(0);
            $table->tinyInteger('status')->comment('1 for padding, 2 for published');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            // foreign key child
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

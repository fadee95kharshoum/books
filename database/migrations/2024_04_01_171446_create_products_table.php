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
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('name', 70);
            $table->string('age', 70);
            $table->text('about', 600);
            $table->tinyInteger('is_published')->default(0);
            $table->tinyInteger('is_best_selling')->default(0);
            $table->string('image_path');            
            $table->string('arabic_file_path')->nullable();
            $table->string('english_file_path')->nullable();
            $table->string('exercises_file_path')->nullable();
            $table->string('short_Story_file_path')->nullable();
            $table->timestamps();
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

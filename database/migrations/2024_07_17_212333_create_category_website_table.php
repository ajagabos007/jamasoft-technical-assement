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
        Schema::create('category_website', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->casadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('website_id')->constrained('websites')->casadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['category_id', 'website_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_website');
    }
};

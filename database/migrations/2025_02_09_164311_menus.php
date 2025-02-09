<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->double('price');
            $table->string('category')->default('Eastern_food');
            $table->string('image_name')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE menus ADD CONSTRAINT chk_category CHECK (category IN ('Eastern_food', 'Western_food', 'Desserts', 'Juices'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
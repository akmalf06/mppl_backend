<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mitra_name');
            $table->string('mitra_wa');
            $table->unsignedBigInteger('stock_number');
            $table->string("image", 256)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('branch_id');

            $table
                ->foreign('branch_id')
                ->on('branches')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("name", 128);
            $table->string("email", 128);
            $table->string("password", 256);
            $table->string("profile_picture", 256);
            $table->boolean("is_supervisor")->default(false);
            $table->bigInteger('role_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned();
            $table->dateTime("created_at");
            $table->dateTime("updated_at");
            $table->dateTime("deleted_at")->nullable();

            $table
                ->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
                
            $table
                ->foreign('branch_id')
                ->references('id')
                ->on('branches')
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
        Schema::dropIfExists('users');
    }
};

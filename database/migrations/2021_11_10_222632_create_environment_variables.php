<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnvironmentVariables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('environment_variables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('environment_id')
                ->nullable();
            $table->string('name');
            $table->string('value');
            $table->foreign('environment_id')
                ->references('id')
                ->on('environments')
                ->cascadeOnDelete();
            $table->unique(['environment_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('environment_variables');
    }
}

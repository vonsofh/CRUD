<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translatables', function (Blueprint $table) {
            $table->increments('id');
            $table->text('translatable_field')->nullable();
            $table->text('translatable_fake')->nullable();
            $table->text('translatable_field_with_mutator')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translatables');
    }
}

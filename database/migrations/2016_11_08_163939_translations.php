<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Translations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('word_id')->unsigned();;
            $table->text('body');
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('word_id')
                ->references('id')->on('words')
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
        //
        Schema::drop('translations');
    }
}

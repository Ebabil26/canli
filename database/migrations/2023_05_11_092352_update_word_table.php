<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->integer('language_id');
        });

        DB::table('words')
            ->update(['language_id' => DB::raw("CASE WHEN language = 'Q' THEN 1 ELSE 2 END")]);

        Schema::table('words', function($table) {
            $table->dropColumn('language');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function($table) {
            $table->dropColumn('language_id');
        });

    }
}

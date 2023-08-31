<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewColumnWords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->integer('targetLanguage_id');
        });

        // Обновление значений в поле targetLanguage_id
        DB::table('words')->where('language_id', 1)->update(['targetLanguage_id' => 2]);
        DB::table('words')->where('language_id', 2)->update(['targetLanguage_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropColumn('targetLanguage_id');

        });
    }
}

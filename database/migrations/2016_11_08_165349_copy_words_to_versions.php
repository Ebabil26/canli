<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyWordsToVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $allWords = \App\Word::all();

        foreach ($allWords as $word)
        {
            $word->translations()->create([
                'body' => $word->translation,
                'status' => $word->status,
                'created_at' => $word->created_at,
                'updated_at' => $word->updated_at
            ]);

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

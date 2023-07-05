<?php

namespace App\Http\Controllers;

use App\Translation;
use Illuminate\Http\Request;

use App\Http\Requests;

class TranslationsController extends Controller
{
    //
    public function approve($translation) {

        $word = $translation->word;

        foreach ($word->translations as $item) {
            $item->status = Translation::WORD_STATUS_PROPOSED;
            $item->save();
        }

        $translation->status = Translation::WORD_STATUS_APPROVED;
        $translation->save();

        return redirect()->action(
            'WordsController@show', ['word' => $word]
        );
    }

    public function disapprove($translation) {
        $translation->status = Translation::WORD_STATUS_PROPOSED;
        $translation->save();

        return redirect()->action(
            'WordsController@show', ['word' => $translation->word]
        );
    }

    public function delete($translation) {
        $translation->status = Translation::WORD_STATUS_DISABLED;
        $translation->save();

        return redirect()->action(
            'WordsController@show', ['word' => $translation->word]
        );
    }
}

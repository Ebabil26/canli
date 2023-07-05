<?php

namespace App\Http\Controllers;

use App\Phrase;
use Illuminate\Http\Request;

use App\Http\Requests;
use Yajra\Datatables\Datatables;

class PhraseController extends Controller
{
    public function getIndex()
    {
        return view('phrases.index');
    }

    public function index() {
        $phrases = Phrase::all();
        return response()->json($phrases);
    }

    public function add(Request $request) {
        Phrase::firstOrCreate(['phrase' => $request->phrase, 'translation' => $request->translation, 'phrase_category_id' => $request->phrase_category_id]);

        return redirect()->action(
            'PhraseController@getIndex'
        );
    }

    public function save(Request $request, $id) {
        $phrase = Phrase::findOrFail($id);

        $phrase->phrase = $request->phrase;
        $phrase->translation = $request->translation;
        $phrase->phrase_category_id = $request->phrase_category_id;

        $phrase->save();

        return redirect()->action(
            'PhraseController@getIndex'
        );
    }

    public function show($id) {
        $phrase = Phrase::findOrFail($id);

        return view('phrases.edit', ['phrase' => $phrase]);
    }

    public function delete($id) {
        $phrase = Phrase::findOrFail($id);

        $phrase->delete();

        return redirect()->action(
            'PhraseController@getIndex'
        );
    }

    public function anyData()
    {
        $phrases = Phrase::with('category');

        return Datatables::of($phrases)
            ->editColumn('id', function ($phrase) {
                return '<a  href="/phrase/' . $phrase->id . '">' . $phrase->id . '</a>';
            })
            ->editColumn('phrase', function ($phrase) {
                return '<a  href="/phrase/' . $phrase->id . '">' . $phrase->phrase . '</a>';
            })
            ->addColumn('category', function ($phrase) {
                return '<i style="font-size: 20px" class="' . $phrase->category->icon  . '"></i>&nbsp;' . $phrase->category->name;
            })
            ->addColumn('action', function ($phrase) {
                return '<a href="/phrase/' . $phrase->id .'/delete" style="color: red">&times;</a>';
            })
            ->make(true);

    }

    public function lastAdded($date = '1970-01-01 00:00:00')
    {
        $phrases = Phrase::where('updated_at', '>', $date)->get();
        return response()->json($phrases);
    }
}

<?php

namespace App\Http\Controllers;

use App\PhraseCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use Yajra\Datatables\Datatables;

class PhraseCategoryController extends Controller
{
    //

    public function getIndex()
    {
        return view('phrasecategories.index');
    }

    public function index() {
        $phraseCategories = PhraseCategory::all();
        return response()->json($phraseCategories);
    }

    public function apiShow($category_id) {
        $phraseCategory = PhraseCategory::where('id', $category_id)->with('phrases')->first();
        return response()->json($phraseCategory);
    }

    public function add(Request $request) {
        PhraseCategory::firstOrCreate(['name' => $request->name, 'icon' => $request->icon]);

        return redirect()->action(
            'PhraseCategoryController@getIndex'
        );
    }

    public function save(Request $request, $id) {
        $phrasecategory = PhraseCategory::findOrFail($id);

        $phrasecategory->name = $request->name;
        $phrasecategory->icon = $request->icon;

        $phrasecategory->save();

        return redirect()->action(
            'PhraseCategoryController@getIndex'
        );
    }

    public function show($id) {
        $phrasecategory = PhraseCategory::findOrFail($id);

        return view('phrasecategories.edit', ['phrasecategory' => $phrasecategory]);
    }

    public function delete($id) {
        $phrasecategory = PhraseCategory::findOrFail($id);

        $phrasecategory->delete();

        return redirect()->action(
            'PhraseCategoryController@getIndex'
        );
    }

    public function anyData()
    {
        return Datatables::of(PhraseCategory::query())
            ->editColumn('id', function ($phrasecategory) {
                return '<a  href="/phrasecategory/' . $phrasecategory->id . '">' . $phrasecategory->id . '</a>';
            })
            ->editColumn('name', function ($phrasecategory) {
                return '<a  href="/phrasecategory/' . $phrasecategory->id . '">' . $phrasecategory->name . '</a>';
            })
            ->editColumn('icon', function ($phrasecategory) {
                return '<i style="font-size: 20px" class="' . $phrasecategory->icon  . '"></i>&nbsp;' . $phrasecategory->icon ;
            })
            ->addColumn('action', function ($phrasecategory) {
                return '<a href="/phrasecategory/' . $phrasecategory->id .'/delete" style="color: red">&times;</a>';
            })
            ->make(true);

    }

    public function lastAdded($date = '1970-01-01 00:00:00')
    {
        $pc = PhraseCategory::where('updated_at', '>', $date)->get();
        return response()->json($pc);
    }
}

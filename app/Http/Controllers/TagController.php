<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

use App\Http\Requests;

class TagController extends Controller
{
    //

    public function index(Request $request) {

        $tags = Tag::all();

        if ($request->isXmlHttpRequest()) {
            return response()->json($tags);
        } else {
            return view('tags.index', ['tags' => $tags]);
        }
    }

    public function add(Request $request) {

        if(!$request->has('value'))
            return false;

        $tag = Tag::firstOrCreate(['value' => trim($request->value)]);

        return redirect()->route('tags');
    }
}

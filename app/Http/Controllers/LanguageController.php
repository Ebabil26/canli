<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        return view('languages.index', ['languages' => $languages]);
    }

    public function create()
    {
        return view('languages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|max:255',
            'code' => 'required|max:50', // новое поле
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $language = new Language();
        $language->id = $request->id;
        $language->language = $request->language;
        $language->code = $request->code; // сохраняем код языка
        $language->save();

        return redirect()->route('languages.index')->with('success', 'Язык успешно создан!');
    }


    public function show($id)
    {
        $language = Language::findOrFail($id);
        return view('languages.show', compact('language'));
    }

    public function edit($id)
    {
        $language = Language::findOrFail($id);
        return view('languages.edit', compact('language'))->with('message', 'Редактирование языка');
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        $language->language = $request->input('language');
        $language->code = $request->input('code');
        $language->save();

        return redirect()->route('languages.index')->with('success', 'Язык успешно обновлен!');
    }


    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        $language->delete();

        return redirect()->route('languages.index')->with('success', 'Язык успешно удален!');
    }
}


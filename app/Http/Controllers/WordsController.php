<?php

namespace App\Http\Controllers;

use App\Language;
use App\Tag;
use App\Translation;
use App\Word;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Resource;
use Yajra\Datatables\Datatables;
use App\Http\Requests;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Support\Facades\Storage;




class WordsController extends Controller
{


    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {

        $tags = \App\Tag::query()->get();
        $languages = Language::all()->sortBy('value');


        return view(
            'words.index', [
                'tags' => $tags,
                'languages' => $languages
            ]
        );
    }

    public function anyDownload(Request $request)
    {
        $tagsIds = $request->input('tag_ids');
        $search = $request->input('search');

        $query = Word::query();

        if (!empty($tagsIds)) {
            $tagIds = explode(',', $tagsIds);
            $tagIds = array_map('intval', $tagIds);

            $query->join('tag_word', 'tag_word.word_id', '=', 'words.id');
            $query->whereIn('tag_word.tag_id', $tagIds);
        }

        if (!empty($search)) {
            $query->where('word', 'like', '%' . $search . '%');
        }

        $words = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=words.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function () use ($words) {
            $columns = ['слово', 'язык', 'словарная статья'];

            $file = fopen('php://output', 'w');
            $columns = array_map(function($v) {
                return iconv('UTF-8', 'CP1251//IGNORE', $v);
            }, $columns);
            fputcsv($file, $columns);

            foreach($words as $word) {
                $word = [
                    $word->word,
                    $word->language,
                    strip_tags($word->translation)
                ];

                $word = array_map(function($v) {
                    return iconv('UTF-8', 'CP1251//IGNORE', $v);
                }, $word);

                fputcsv($file, $word);
            }
            fclose($file);

        }, Response::HTTP_OK, $headers);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request)
    {
        $columns = $request->input('columns');

        $builder = Word::query()->with('tags', 'language');

        if (!empty($columns[4]['search']['value'])) {
            $tagIds = explode(',', $columns[4]['search']['value']);
            $tagIds = array_map('intval', $tagIds);

            $builder->join('tag_word', 'tag_word.word_id', '=', 'words.id');
            $builder->whereIn('tag_word.tag_id', $tagIds);
        }

        return Datatables::of($builder)
            ->editColumn('id', function ($word) {
                return '<a  href="/word/' . $word->id . '" target="_blank">' . $word->id . '</a>';
            })
            ->editColumn('word', function ($word) {
                return '<a  href="/word/' . $word->id . '" target="_blank">' . $word->word . '</a>';
            })
            ->editColumn('tags', function ($word) {
                /** @var \Illuminate\Database\Eloquent\Collection $tags */
                $tags = $word->tags;

                $cellValue = $tags->implode('value', ', ');

                return $cellValue;
            })
            ->make(true);

    }

    public function show($word)
    {
        $allTags = Tag::all()->sortBy('value');

        $allLanguages = Language::all()->sortBy('value');

        $selectedTagIds = [];

        foreach ($word->tags as $tag)
        {
            $selectedTagIds[] = $tag->id;
        }

        return view('words.edit', ['word' => $word, 'tags' => $allTags, 'languages' => $allLanguages, 'selectedTagIds' => $selectedTagIds]);
    }

    public function delete($word)
    {
        $word->delete();

        return redirect()->action(
            'WordsController@getIndex'
        );
    }


    public function wordAddAdmin(Request $request)
    {
        $word = Word::firstOrCreate([
            'word' => $request->word,
            'language_id' => $request->language
        ]);

        return redirect()->action(
            'WordsController@show', ['word' => $word]
        );
    }

    public function add(Request $request)
    {
        if(!$request->word || !$request->translation) {
            return response()->json([]);
        }

        if((int)$request->id > 0) {
            $word = Word::find((int)$request->id);
            $word->translation = $request->input('translation');
            $word->word_formatted =  $request->input('word_formatted');
            $word->word_latin =  $request->input('word_latin');
            $word->word_latin_formatted =  $request->input('word_latin_formatted');
            $word->language_id = $request->input('language');


            $word->save();
        } else if (trim($request->word) != '') {
            $word = Word::firstOrCreate([
                'word' => $request->word,
                'translation' => $request->input('translation'),
                'word_formatted' => $request->input('word_formatted') || '',
                'word_latin' => $request->input('word_latin') || '',
                'word_latin_formatted' => $request->input('word_latin') || '',
                'language_id' => $request->language_id,
            ]);

            $authorName = $request->get('authorName');
            $authorMail = $request->get('authorMail');
            $authorPhone = $request->get('authorPhone');

            if($word) {
                Mail::send('words.email', ['authorName' => $authorName, 'authorMail' => $authorMail, 'authorPhone' => $authorPhone, 'word' => $word], function($message) {
                    $message->to('canlilugat80@gmail.com', 'To Website')
                        ->subject('В приложение Canli Lugat предлагают перевод');
                });
            }
        }

        return response()->json($word);
    }

    public function AddTranslation($word, Request $request)
    {
        $word->translation = $request->input('translation');
        $word->word_formatted = $request->input('word_formatted');
        $word->word_latin = $request->input('word_latin');
        $word->word_latin_formatted = $request->input('word_latin_formatted');
        $word->word = $request->input('word');
        $word->language_id = $request->input('language');

        $selectedTags = ($request->has('tags')) ? $request->input('tags') : [];

        $word->tags()->sync($selectedTags);

        $word->save();

        return redirect()->action(
            'WordsController@show', ['word' => $word]
        );
    }

    public function index($language, $offset = 0, $limit = 100)
    {
        $words = Word::where('language', $language)->where('status', Word::WORD_STATUS_APPROVED)->skip($offset)->take($limit)->get();

        return response()->json($words);
    }

    public function search($word)
    {
        $word = Word::where('word', mb_strtolower($word))->where('status', Word::WORD_STATUS_APPROVED)->get();

        return response()->json($word);
    }

    public function lastAdded($date = '1970-01-01 00:00:00')
    {

        //if ($date == '1970-01-01 00:00:00') {
        //    return redirect()->to('/init/words.json');
        //}

        $words = [];

        foreach (Word::where('updated_at', '>', $date)->get() as $word) {

            //if(!$translation = $word->translations->where('status', Translation::WORD_STATUS_APPROVED)->first())
            //continue;

            $output = $word;
            $output->translation = $word->translation;
            $output->status = Translation::WORD_STATUS_APPROVED;
            //unset($output->translations);

            $words[] = $output;

        }

        return response()->json($words);
    }

    public function translate(Request $request)
    {
        $sourceLanguage = $request->input('source');
        $targetLanguage = $request->input('target');
        $word = $request->input('q');
    
        // Поиск перевода в базе данных с использованием Eloquent
        $translation = Translation::where('source_language', $sourceLanguage)
            ->where('target_language', $targetLanguage)
            ->where('word', $word)
            ->first();
    
        // Проверка наличия перевода
        if ($translation) {
            $translationText = $translation->translation;
        } else {
            $translationText = 'Translation not found';
        }
    
        $response = [
            'source' => $sourceLanguage,
            'target' => $targetLanguage,
            'word' => $word,
            'translation' => $translationText,
        ];
    
        return response()->json($response); // Возврат JSON-ответа с использованием метода json()
    }
    /*
    public function translate(Request $request)
{
    $sourceLanguage = $request->input('source');
    $targetLanguage = $request->input('target');
    $word = $request->input('q');

    // Загрузка базы данных слов из JSON файла
    $database = json_decode(file_get_contents(storage_path('app/words.json')), true);

    // Поиск перевода в базе данных
    $translation = $this->findTranslation($database, $sourceLanguage, $targetLanguage, $word);

    $response = [
        'source' => $sourceLanguage,
        'target' => $targetLanguage,
        'word' => $word,
        'translation' => $translation,
    ];

    // Преобразование массива в строку без кавычек
    $jsonResponse = '{' . implode(',', array_map(function ($key, $value) {
        return $key . ':' . $value;
    }, array_keys($response), array_values($response))) . '}';

    // Возврат отформатированного JSON-ответа
    return response($jsonResponse)
        ->header('Content-Type', 'application/json')
        ->header('charset', 'utf-8');
}
*/
    
    private function findTranslation($database, $sourceLanguage, $targetLanguage, $word)
    {
        foreach ($database as $entry) {
            if ($entry['language'] == $sourceLanguage) {
                if (isset($entry['word']) && $entry['word'] == $word) {
                    return $entry['translation'];
                }
            }
        }

        return 'Translation not found';
    }

    public function getWordHistory() //SS: метод возвращает историю запросов пользователя
    {
        $history = DB::table('word_requests')
            ->select('word')
            ->orderBy('created_at', 'desc')
            ->get();

        return $history;
    }


    public function suggest(Request $request) // SS: метод возвращает другие слова похожие на введенное слово
    {
        $inputWord = $request->input('word');

        // Получить слова из базы данных, которые похожи на введенное слово
        $similarWords = Word::where('name', 'LIKE', '%' . $inputWord . '%')->get();

        // Создать массив для хранения рекомендаций
        $suggestions = [];

        // Перебрать похожие слова и создать рекомендации
        foreach ($similarWords as $word) {
            // Создать рекомендацию с правильным описанием
            $suggestion = [
                'word' => $word->name,
                'description' => $word->description,
            ];

            // Добавить рекомендацию в массив
            $suggestions[] = $suggestion;
        }

        // Вернуть рекомендации в формате JSON
        return response()->json($suggestions);
    }


    public function export()
    {
        $words = Word::get();

        $json_data = json_encode($words);

        echo ($json_data);
    }
}

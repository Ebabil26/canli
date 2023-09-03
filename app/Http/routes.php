<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::group(['middleware' => ['auth']], function() {

    Route::resource('languages', 'LanguageController');
    Route::get('/languages/create', 'LanguageController@create')->name('languages.create');
    Route::post('/languages', 'LanguageController@store')->name('languages.store');
    Route::get('/languages/{language}/edit', 'LanguageController@edit')->name('languages.edit');





    // категории разговорника
    Route::controller('phrasecategories', 'PhraseCategoryController', [
        'anyData'  => 'phrasecategories.data',
        'getIndex' => 'phrasecategories',
    ]);
    Route::get('phrasecategory/{id}', 'PhraseCategoryController@show');
    Route::post('phrasecategory', 'PhraseCategoryController@add');
    Route::post('phrasecategory/{id}/save', 'PhraseCategoryController@save');
    Route::get('phrasecategory/{id}/delete', 'PhraseCategoryController@delete');


    // фразы разговорника
    Route::controller('phrases', 'PhraseController', [
        'anyData'  => 'phrases.data',
        'getIndex' => 'phrases',
    ]);
    Route::get('phrase/{id}', 'PhraseController@show');
    Route::post('phrase', 'PhraseController@add');
    Route::post('phrase/{id}/save', 'PhraseController@save');
    Route::get('phrase/{id}/delete', 'PhraseController@delete');


    Route::controller('words', 'WordsController', [
        'anyData'  => 'words.data',
        'getIndex' => 'words',
        'download' => 'words.download',
    ]);

    Route::get('word/export', 'WordsController@export');
    Route::get('word/{word}', 'WordsController@show');
    Route::get('word/{word}/delete', 'WordsController@delete');
    Route::post('word/add', 'WordsController@wordAddAdmin');
    Route::post('word/{word}/translation', 'WordsController@AddTranslation');

    Route::get('translation/{translation}/approve', 'TranslationsController@approve');
    Route::get('translation/{translation}/disapprove', 'TranslationsController@disapprove');
    Route::get('translation/{translation}/delete', 'TranslationsController@delete');

    Route::get('wordlist/{language}/{offset}/{limit}', 'WordsController@index');
    Route::get('wordsearch/{word}', 'WordsController@search');

    Route::controller('announcements', 'AnnouncementsController', [
        'anyData'  => 'announcements.data',
        'getIndex' => 'announcements',
    ]);
    Route::post('announcement', 'AnnouncementsController@add');



    //API: загрузка клиентских изображений
    Route::post('photos', array('as' => 'savePhotos', 'uses' => 'AnnouncementsController@savePhotos'));

    Route::get('/home', 'HomeController@index');
    Route::get('/import', 'HomeController@wordsImportShow');
    Route::post('/upload', 'HomeController@upload');

    Route::get('/tags', 'TagController@index')->name('tags');
    Route::post('/tags/add', 'TagController@add');
    Route::post('/tags/{tag_id}/edit', 'TagController@edit');
    Route::get('/tags/{tag_id}/delete', 'TagController@delete');


});

Route::group(['prefix' => 'api'], function () {
    Route::get('phrasecategories/last-added/{date?}', 'PhraseCategoryController@lastAdded');
    Route::get('phrases/last-added/{date?}', 'PhraseController@lastAdded');

    Route::get('phrasecategories', 'PhraseCategoryController@index');
    Route::get('phrasecategories/{category_id}', 'PhraseCategoryController@apiShow');


});

Route::post('token', 'TokenController@add');
Route::post('word', 'WordsController@add');
Route::get('last-added/{date?}', 'WordsController@lastAdded');

Route::match(['GET', 'POST'], '/translate', 'WordsController@translate')->name('translate');
Route::post('/translate', 'WordsController@translate')->name('translate');
Route::get('/word', 'WordsController@findTranslation')->name('word');


Route::get('announcementlist', 'AnnouncementsController@index');
Route::get('announcement/{id}', 'AnnouncementsController@show');

Route::auth();



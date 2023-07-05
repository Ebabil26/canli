@extends('layouts.master')

@section('content')
    <h1><a href="/phrases">&larr;</a> {{ $phrase->phrase }} <a title="Удалить" style="color: red"
                                                         onclick="return confirm('точно?')"
                                                         href="/phrase/{{ $phrase->id }}/delete">&times;</a></h1>

    {!! Form::open(['url' => '/phrase/' . $phrase->id . '/save', 'method' => 'post']) !!}

    <div class="form-group">
        <label>Фраза</label>
        <input type="text" class="form-control" value="{{ $phrase->phrase }}" name="phrase">
    </div>

    <div class="form-group">
        <label>Перевод</label>
        <input type="text" class="form-control" value="{{ $phrase->translation }}" name="translation">
    </div>

    <div class="form-group">
        <label>Категория</label>
        <select class="form-control" name="phrase_category_id">
            @foreach(\App\PhraseCategory::all() as $phraseCategory)
            <option value="{{ $phraseCategory->id }}" @if($phraseCategory->id == $phrase->phrase_category_id) selected @endif>{{ $phraseCategory->name }}</option>
            @endforeach
        </select>
    </div>



    <button type="submit" class="btn">Сохранить</button>

    {!! Form::close() !!}



@stop

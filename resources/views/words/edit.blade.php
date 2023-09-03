@extends('layouts.master')

@section('content')
    <h1><a href="/words">&larr;</a> {{ $word->word }} <a title="Удалить" style="color: red"
                                                         onclick="return confirm('точно?')"
                                                         href="/word/{{ $word->id }}/delete">&times;</a></h1>

    {!! Form::open(['url' => '/word/' . $word->id . '/translation', 'method' => 'post']) !!}

    <div class="form-group">
        <label>Слово</label>
        <input type="text" class="form-control" value="{{ $word->word }}" name="word">
    </div>

    <div class="form-group">
        <label>Слово на латинице</label>
        <input type="text" class="form-control" value="{{ $word->word_latin }}" name="word_latin">
    </div>

    <div class="form-group">
        <label>Диалекты</label>
        @foreach($tags as $tag)
            <div style="display: inline-block; margin-right: 20px; margin-left: 20px; transform: scale(1.1);">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @if(in_array($tag->id, $selectedTagIds)) checked @endif>
                <label>{{ $tag->value }}</label>
            </div>
        @endforeach
    </div>

    <div class="form-group">
        <label for="language" class="control-label">Язык:</label>
        <select class="form-control" id="language" name="language">
            @foreach ($languages as $language)
                <option value="{{ $language->id }}" {{ $word->language->id === $language->id ? 'selected' : '' }}>{{ $language->language }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
    <label for="targetLanguage" class="control-label">Целевой язык:</label>
    <select class="form-control" id="targetLanguage" name="targetLanguage">
        @foreach ($languages as $targetLanguage)
            <option value="{{ $targetLanguage->id }}" {{ $word->targetLanguage_id === $targetLanguage->id ? 'selected' : '' }}>
                {{ $targetLanguage->language }}
            </option>
        @endforeach
    </select>
</div>




    <div class="form-group">
        <label>Слово на латинице форматированное</label>
        <textarea name="word_latin_formatted"  style="height: 100px;">
            {{ $word->word_latin_formatted }}
        </textarea>
    </div>

    <div class="form-group">
        <label>Слово форматированное</label>
        <textarea name="word_formatted"  style="height: 100px;">
            {{ $word->word_formatted }}
        </textarea>
    </div>

    <div class="form-group">
        <label>Перевод</label>
        <textarea name="translation" style="height: 500px;">
            {{ $word->translation }}
        </textarea>
    </div>

    <button type="submit" class="btn">Сохранить</button>

    {!! Form::close() !!}





@stop

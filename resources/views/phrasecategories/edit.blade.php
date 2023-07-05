@extends('layouts.master')

@section('content')
    <h1><a href="/phrasecategories">&larr;</a> {{ $phrasecategory->name }} <a title="Удалить" style="color: red"
                                                         onclick="return confirm('точно?')"
                                                         href="/phrasecategory/{{ $phrasecategory->id }}/delete">&times;</a></h1>

    {!! Form::open(['url' => '/phrasecategory/' . $phrasecategory->id . '/save', 'method' => 'post']) !!}

    <div class="form-group">
        <label>Название</label>
        <input type="text" class="form-control" value="{{ $phrasecategory->name }}" name="name">
    </div>

    <div class="form-group">
        <label>Иконка</label>
        <input type="text" class="form-control" value="{{ $phrasecategory->icon }}" name="icon">
    </div>



    <button type="submit" class="btn">Сохранить</button>

    {!! Form::close() !!}



@stop

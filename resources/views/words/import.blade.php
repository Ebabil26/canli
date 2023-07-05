@extends('layouts.master')

@section('content')
<h1>Импорт</h1>

@if(Session::has('added'))
    <div class="alert-box success">
        <h2>{!! Session::get('added') !!} слов добавлено</h2>
    </div>
@endif

@if(Session::has('updated'))
    <div class="alert-box success">
        <h2>{!! Session::get('updated') !!} слов обновлено</h2>
    </div>
@endif



{!! Form::open(['url' => '/upload', 'method' => 'post', 'files'=>true]) !!}
<p>
    <label>Файл со словами (слово;;перевод)</label>

    <br />
    <b>язык:</b><br />
    <label>
        <input type="radio" name="language" value="{!! \App\Word::LANG_QIRIM !!}" /> крымскотатарский
    </label>
    <br />
    <label>
        <input type="radio" name="language" value="{!! \App\Word::LANG_RUSSIAN !!}" /> русский
    </label>
    <br />
    <input type="file" class="form-control" name="importfile">
</p>
<p>
    <button class="btn btn-default" type="submit">Поехали</button>
</p>
{!! Form::close() !!}
@endsection

@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Языки</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Язык</th>
                                <th>Code</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($languages as $language)
                                <tr>
                                    <td>{{ $language->id }}</td>
                                    <td>{{ $language->language }}</td>
                                    <td>{{ $language->code }}</td>
                                    <td>
                                        <a href="{{ route('languages.edit', $language->id) }}" class="btn btn-sm btn-primary">Изменить</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            <a href="{{ route('languages.create') }}" class="btn btn-primary">Добавить новый язык</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Добавление языка</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('languages.store') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <label for="language">Язык:</label>
                                <input type="text" name="language" id="language" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="code">Code:</label>
                                <input type="text" name="code" id="code" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить язык</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



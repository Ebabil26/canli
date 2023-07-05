@extends('layouts.master')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            Добавить диалект
        </div>
        <div class="panel-body">
            {!! Form::open(['url' => '/tags/add', 'method' => 'post']) !!}

            <div class="form-group">
                <label for="value" class="control-label">Диалект:</label>
                <input name="value" type="text" class="form-control" id="value">
            </div>

            <button class="btn" type="submit">Добавить</button>

            {!! Form::close() !!}
        </div>
    </div>



    <table class="table table-bordered" id="tags-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Диалект</th>
            <th>Добавлено</th>
            <th>Действия</th>

        </tr>
        </thead>
        <tbody>
        @foreach($tags as $tag)
            <tr>
                <td>{{$tag->id}}</td>
                <td>{{$tag->value}}</td>
                <td>{{$tag->created_at}}</td>
                <td>
                    <a onclick="return confirm('Точно?')" href="/tags/{{$tag->id}}/delete">удалить</a>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot></tfoot>
    </table>
@stop

@push('scripts')
<script>
    $(function () {
        $('#tags-table').DataTable({
            pageLength: 100,
            order: [[ 2, "desc" ]]
        });
    });
</script>
@endpush

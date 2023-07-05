@extends('layouts.master')

@section('content')

    {!! Form::open(['url' => '/phrase', 'method' => 'post', 'class' => 'add-word well']) !!}
    <h4>Добавить фразу разговорника </h4>
    <p>
        <label>Фраза</label>
        <input type="text" class="form-control" name="phrase">
    </p>

    <p>
        <label>Перевод </label>
        <input type="text" class="form-control" name="translation">
    </p>

    <p>
        <label>Категория</label>
        <select class="form-control" name="phrase_category_id">
            @foreach(\App\PhraseCategory::all() as $phraseCategory)
                <option value="{{ $phraseCategory->id }}">{{ $phraseCategory->name }}</option>
            @endforeach
        </select>
    </p>

    <p>
        <button type="submit" class="btn btn-primary">Добавить</button>
    </p>
    {!! Form::close() !!}

    <table class="table table-bordered" id="phrase-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Фраза</th>
            <th>Перевод</th>
            <th>Категория</th>
            <th>Удалить</th>

        </tr>
        </thead>
        <tfoot></tfoot>
    </table>
@stop

@push('scripts')
<script>
    $(function () {
        $('#phrase-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('phrases.data') !!}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'phrase', name: 'phrase'},
                {data: 'translation', name: 'translation'},
                {data: 'category', name: 'category'},

                //{data: 'created_at', name: 'created_at'},
                //{data: 'updated_at', name: 'updated_at'},

                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val ? val : '', true, false).draw();
                            });
                });
            }
        });
    });

</script>
@endpush
@extends('layouts.master')

@section('content')
    {!! Form::open(['url' => '/word/add', 'method' => 'post', 'class' => 'add-word well']) !!}
    <h4>Добавить слово </h4>
    <p>
        <label>Слово</label>
        <input type="text" class="form-control" name="word">
    </p>
    <p>
        <label>Язык</label>
        <select class="form-control" name="language">'
            @foreach ($languages as $language)
                <option value="{{ $language->id }}">{{ $language->language }}</option>
            @endforeach
        </select>
    </p>
    <p style="float: right">
        <a href="{{ route('languages.index') }}">Список языков</a>
    </p>

    <p>
        <button type="submit" class="btn btn-primary">Добавить</button>
    </p>

    {!! Form::close() !!}


    <div style="float: left">
        <label>Поиск по диалектам:&nbsp;
            <select id="words-tags-filter" multiple style="width: 500px" >
                @foreach($tags as $tag)
                    <option value="{{ $tag['id'] }}">{{ $tag['value'] }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <div style="padding-top: 4px;">
        &nbsp; &rarr; <a href="/words/download" id="download-link">скачать сsv</a>
    </div>

    <table class="table table-bordered" id="words-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Слово</th>
            <th>Язык</th>
            <th>Перевод</th>
            <th>Диалекты</th>
            <!--th>Добавлено</th-->
            <th>Последние изменения</th>
            <!--th>Статус</th-->
            <!--th>Переводов</th-->


        </tr>
        </thead>
        <tfoot></tfoot>
    </table>
@stop

@push('scripts')
<script>
    $(function () {

        $('#download-link').on('click', function (e) {
            e.preventDefault();

            var searchString = $('#words-table_wrapper input[type="search"]').val();
            var link = '/words/download';
            link += '?tag_ids=' + $('#words-tags-filter').val();
            link += '&search=' + encodeURIComponent(searchString)

            window.open(link);
        });

        $('#words-tags-filter').select2({

            tags: true,
        });

        $('#words-tags-filter').on('change', function (event) {
            var tagIds = $(this).val();
            $('#words-table').DataTable().column(4).search(tagIds);
            $('#words-table').DataTable().draw();
        });

        $('#words-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('words.data') !!}',
            pageLength: 100,
            columns: [
                {data: 'id'},
                {data: 'word'},
                {data: 'language.code', "searchable":     false},
                {data: 'translation', "searchable":     false},
                {data: 'tags', "searchable":     false,  "orderable": false},
                //{data: 'created_at', name: 'lastTranslations.created_at'},
                {data: 'updated_at'},
                //{data: 'status'},
                //{data: 'history_count',  "searchable":     false}
            ],
            order: [[ 5, "desc" ]],
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



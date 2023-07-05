@extends('layouts.master')

@section('content')

    {!! Form::open(['url' => '/phrasecategory', 'method' => 'post', 'class' => 'add-word well']) !!}
    <h4>Добавить категорию разговорника </h4>
    <p>
        <label>Название</label>
        <input type="text" class="form-control" name="name">
    </p>

    <p>
        <label>Иконка (<a target="_blank" href="http://ionicons.com/">http://ionicons.com/</a>)</label>
        <input type="text" class="form-control" name="icon">
    </p>


    <p>
        <button type="submit" class="btn btn-primary">Добавить</button>
    </p>
    {!! Form::close() !!}

    <table class="table table-bordered" id="phrasecat-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Название</th>
            <th>Иконка</th>
            <th>Удалить</th>


        </tr>
        </thead>
        <tfoot></tfoot>
    </table>
@stop

@push('scripts')
<script>
    $(function () {
        $('#phrasecat-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('phrasecategories.data') !!}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'icon', name: 'icon'},

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
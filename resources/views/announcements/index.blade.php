@extends('layouts.master')

@section('content')

    <p>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#announcementModal">Добавить
            объявление
        </button>
    </p>

    <table class="table table-bordered" id="announcements-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Заголовок</th>

            <th>Добавлено</th>
            <th>Изменено</th>
            <th>Статус</th>
            <th>Действия</th>

        </tr>
        </thead>
        <tfoot></tfoot>
    </table>
@stop

@push('scripts')
<script>
    $(function () {
        $('#announcements-table').DataTable({
            pageLength: 100,
            processing: true,
            serverSide: true,
            ajax: '{!! route('announcements.data') !!}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'header', name: 'header'},
                //{data: 'translation', name: 'translation', "searchable":     false},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'status', name: 'status'},
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


    $('#announcementModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var announcementId = button.data('announcement-id');
        var modal = $(this);

        if (announcementId) {
            $.get('/announcement/' + announcementId, function (data) {
                //console.log(data.word);
                modal.find('#header').val(data.header);
                // modal.find('#body').setContent(data.body);
                tinymce.get('body').setContent(data.body);
                modal.find('#status').val(data.status);
                modal.find('#photo_file_name').val(data.image);
                modal.find('#photo_img').attr('src', '/uploads/announcement_photos/' + data.image);
                modal.find('#saveButton').attr('data-announcement-id', announcementId);
            })
        }
        else {
            modal.find('#header').val('');
            modal.find('#body').val('');
            modal.find('#status').val('');
            modal.find('#photo_file_name').val('');
            modal.find('#photo_img').val('');
            modal.find('#saveButton').attr('data-announcement-id', '');
        }

    })

    $('#announcementModal form').on('submit', function (e) {
        e.preventDefault();

        var header = $('#header').val(),
                id = $('#saveButton').attr('data-announcement-id'),
                body =  tinymce.get('body').getContent(),
                status = $('#status').val(),
                photo_file_name = $('#photo_file_name').val();

        if (!header || !body) {
            alert('Не все поля заполнены!');
            return false;
        }

        var announcement = {
            id: id,
            header: header,
            body: body,
            status: status,
            image: photo_file_name
        };

        $('#saveButton').attr('disabled', 'disabled');

        $.post('/announcement', announcement, function (response) {
            document.location.reload();
        })

        return false;
    })

    var photoLoader = {
        init: function() {
            var _this = this;

            $('#add_photo').on('change', function() {
                _this.addPhoto();
            })
        },
        addPhoto: function () {
            var _this = this;

            var photoInput = $('#add_photo');
            var url = photoInput.attr('data-action');
            var photo = photoInput[0].files[0];

            if (_this.validatePhoto(photo) === false) {
                return;
            }

            var data = new FormData();
            data.append('photos[]', photo);

            $.ajax({
                url: url,
                type: "POST",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    _this.addUploadedPhoto(response);
                }
            });
        },

        addUploadedPhoto: function (response) {
            var _this = this;
            var photoInput = $('#add_photo');
            var photosContainer = $('#photosContainer');

            if (!response || response.error !== 0 || !response.result || !response.result.photos) {
                var message = 'Произошла ошибка во время загрузки.';
                photoInput.parent('div').find('span.cr_error').remove();
                photoInput.parent('div').append('<span class="cr_error">' + message + '</span>');
                return false;
            }

            var photos = response.result.photos;

            photos.forEach(function(v,i){
                var name = v.original_name;
                var id = v.id;
                var file_name = v.file_name;

                photosContainer.html('<input type="hidden" name="photos" id="photo_file_name" value="' + file_name + '">' +
                        '<div class="photo_el"><img id="photo_img" src="/uploads/announcement_photos/' + file_name + '" width="100%"/></div>'
                );

            });

            //Сбрасываем выбранный файл
            photoInput.wrap('<form>').closest('form').get(0).reset();
            photoInput.unwrap();
            //photoInput.stopPropagation();
            //photoInput.preventDefault();
        },

        validatePhoto: function (photo) {
            var _this = this;

            var photoInput = $('#add_photo');
            var photosContainer = $('#photosContainer');
            var message = '';
            var imgReg = /^image\//;

            if (photosContainer.find('input[name="photos[]"]').length >= 6) {
                message = 'Превышен лимит по количеству файлов (не больше 6)';
            }
            //2Мб макс размер файла
            if (photo.size > 2 * 1024 * 1024) {
                message = 'Слишком большой файл';
            }
            //только изображения
            else if (imgReg.test(photo.type) === false) {
                message = 'Недопустимый тип файла';
            }


            if (message != '') {
                photoInput.parent('div').find('span.cr_error').remove();
                photoInput.parent('div').append('<span class="cr_error">' + message + '</span>');
                return false;
            }
            else {
                photoInput.parent('div').find('span.cr_error').remove();
                return true;
            }
        }
    }

    photoLoader.init();

</script>
@endpush


<div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open() !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Редактирование объявления</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="word" class="control-label">Заголовок:</label>
                        <input type="text" class="form-control" id="header">
                    </div>
                    <div class="form-group">
                        <label for="translation" class="control-label">Содержание:</label>
                        <textarea class="form-control" id="body"></textarea>
                    </div>


                    <div class="form-group">
                        {!! Form::label('photo', 'Фотографии') !!}
                        <div id="photosContainer">
                            <input type="hidden" name="photos" id="photo_file_name" value="">
                            <div class="photo_el"><img id="photo_img" src="" width="100%"/></div>
                        </div>
                        {!! Form::file('photo', ['class' => 'file-input form-control', 'id' => 'add_photo', 'data-action' => route('savePhotos')]) !!}
                    </div>

                    <div class="form-group">
                        <label for="status" class="control-label">Статус:</label>
                        <select id="status">
                            @foreach(App\Announcement::$statuses as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary" data-word-id="" id="saveButton">Сохранить</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
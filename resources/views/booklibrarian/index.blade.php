@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    @if (Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif
    <div class="form-group">
        <label id="userbl" class=" control-label">{{ trans('booklibrarian.find_user') }} :</label>
        <input type="text" id="userb" name="userb" value="" class="form-control">
        <input type="hidden" id="userb_id" value="">
    </div>
    <div id="selected_userb" name="selected_userb">
        @if(isset($userb))
            @include($type.'/issuereturn')
        @endif
    </div>

    <div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{trans('booklibrarian.find_book_title')}}</h4>
                </div>
                <div class="modal-body" id="modal-book">
                    <div class="form-group">
                        <label id="bookl" class="control-label">{{ trans('booklibrarian.find_book') }} :</label>
                        <input type="text" id="book" name="book" value="" class="form-control">
                    </div>
                    <div id="select_book" name="select_book">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{ trans('booklibrarian.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $("#bookModal").on('hidden.bs.modal', function (e) {
            $("#select_book").html("");
            $("#book").val("");
        });

        $('#userb').autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: "POST",
                    url: "{{url('/booklibrarian/getusers')}}",
                    data: {userb: $("#userb").val(), _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (userbs) {
                        response($.each(userbs, function (index, value) {
                            return {
                                label: value.label,
                                value: value.value
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                var id = ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "{{url('/booklibrarian/issuereturn')}}" + "/" + id,
                    success: function (data) {
                        $('#selected_userb').html(data);
                        $('#userb_id').val(id);
                    }
                });
                $(this).val(ui.item.label);
                return false;
            }
        });


        function returnBook(id) {
            $.ajax({
                type: "GET",
                url: "{{ url('/booklibrarian/return') }}" + "/" + id,
                success: function () {
                    $('tr#book'+id).remove();
                }
            });
        }

        $('#book').autocomplete({
            appendTo: '#modal-book',
            source: function (request, response) {
                $.ajax({
                    type: "POST",
                    url: "{{url('/booklibrarian/getbooks')}}",
                    data: {book: $("#book").val(), _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (books) {
                        response($.each(books, function (index, value) {
                            return {
                                label: value.label,
                                value: value.value
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                var id = ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "{{url('/booklibrarian/book')}}" + "/" + id,
                    success: function (data) {
                        $('#select_book').html(data);
                    }
                });

                $(this).val(ui.item.label);
                return false;
            }
        });

        function issueBook(bookId) {
            var userb_id = $('#userb_id').val();
            $.ajax({
                type: "GET",
                url: "{{ url('/booklibrarian/issuebook') }}" + "/" + userb_id + "/" + bookId,
                success: function (data) {
                    $('#bookModal').modal('hide');
                    //window.location.reload();
                    $('#selected_userb').html(data);

                }
            });
        }
    </script>
@stop
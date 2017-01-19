@extends('layouts.admin')



@section('title', 'LIST OF CATEGORIES')



@section('content')

    <div class="content_form">

        <div class="admincat_wrapper">

            <div class="admincat_wrapper__new admincat_wrapper_maintenance__new">

                <form class="add_form" role="form" method="POST" action="{{ url('/maintenance_warnings/store') }}">

                    {{ csrf_field() }}
                    <div class="maintenance_inputs_wrapper">
                        <input type="text"  placeholder="Text" name="text">
                        <input type="text"  placeholder="From(2017-01-20)" name="date_from">
                        <input type="text"  placeholder="To(2017-01-20)" name="date_to">
                    </div>

                    <button type="submit">Create new maintenance warning</button>

                </form>

            </div>



            <div class="admincat_wrapper__items">

                @if($warnings->count()> 0)

                    @foreach($warnings as $warning)

                    <div class="item item__maintenance clearfix" id="item_{{$warning->id}}">

                        <p>{{$warning->text}}</p>
                        <p>Date from: <span>{{$warning->date_from}}</span></p>
                        <p>Date to: <span>{{$warning->date_to}}</span></p>

                        <input type="text" name="text" id="category_{{$warning->id}}_text">
                        <input type="text" name="date_from" id="category_{{$warning->id}}_date_from">
                        <input type="text" name="date_to" id="category_{{$warning->id}}_date_to">

                        <button class="saveButton save_maintenance__btn" data-category_id="{{$warning->id}}">Save</button>

                        <div class="buttons">
                            <button><img src="{{asset('img/Admin/edit.png')}}" alt="alt"></button>
                            <a class="popup-with-move-anim" href="#small-dialog4"><button class="deleteButton delete_maintenance__btn" data-category_id="{{$warning->id}}"><img src="{{asset('img/Admin/delete.png')}}" alt="alt"></button></a>
                        </div>
                    </div>

                    @endforeach

                @endif

            </div>

        </div>

    </div>



    <script src="https://code.jquery.com/jquery-3.0.0.js" integrity="sha256-jrPLZ+8vDxt2FnE1zvZXCkCcebI/C8Dt5xyaQBjxQIo=" crossorigin="anonymous"></script>

    <script>

        var category_id;
        var newCat = 1;
        $('.deleteButton').click(function () {
            // if(confirm("Are you sure? All data related with this category will be deleted! (Cards, Orders, Requests e.t.c...)")){
            //     var category_id = $(this).attr('data-category_id');
            //     $.ajax({
            //         url: '/api/category/'+ category_id,
            //         type: 'DELETE',
            //         success: function (data) {
            //             switch(data){
            //                 case 'success':
            //                     $('#item_' + category_id).remove();
            //                     break;
            //                 default:
            //                     alert('Error has been occurred!');
            //             }
            //         }
            //     });
            // }
            // console.log(category_id);

            if ($(this).hasClass("delete_maintenance__btn")) {

                if (confirm("You would like to remove this maintenance?")) {
                    var maint_id = $(this).attr('data-category_id');
                    $.ajax({
                        url: '/api/maintenance_warnings/'+ maint_id,
                        type: 'DELETE',
                        success: function (data) {
//                            switch(data){
//                                case 'success':
                            $('#item_' + maint_id).remove();
                            alert("Maintenance has been successfully deleted!");
                            $.magnificPopup.close();
//                                    break;
//                                default:
//                                    alert('Error has been occurred!');
//                            }
                        }
                    });
                }

            } else {

                category_id = $(this).attr('data-category_id');
                $("#category option").attr("disabled", false);
                $("#category option[value='" + category_id + "']").attr("disabled", true);
                $("#small-dialog3 h1").text('To delete "' + $(this).parents(".item").find(">p").text() + '" , please choose a new label for jobs in this category:');

            }
        });

        $("#category").on("change", function() {
            newCat = $("#category option:selected").attr("value");
        });

        $("#small-dialog3 .buttons .buttons_ok").on("click", function() {
                
                $.ajax({
                    url: '/api/category/'+ category_id + '/' + newCat,
                    type: 'DELETE',
                    success: function (data) {
                        switch(data){
                            case 'success':
                                $('#item_' + category_id).remove();
                                $.magnificPopup.close();
                                alert("Category has been successfully deleted!");
                                break;
                            default:
                                alert('Error has been occurred!');
                        }
                    }
                });
               
                console.log(category_id);
            });


        $('.saveButton').click(function () {
            if ($(this).hasClass("save_maintenance__btn")) {
                var category_id = $(this).attr('data-category_id');
                var text  = $('#category_'+category_id+'_text').val();
                var date_from  = $('#category_'+category_id+'_date_from').val();
                var date_to  = $('#category_'+category_id+'_date_to').val();
                $.ajax({
                    url: '/api/maintenance_warnings/'+ category_id,
                    type: 'PUT',
                    data: { text:text, date_from:date_from, date_to:date_to} ,
                    success: function (data) { }
                });
            } else {
                var category_id = $(this).attr('data-category_id');
                var title  = $('#category_'+category_id+'_title').val();
                $.ajax({
                    url: '/api/category/'+ category_id,
                    type: 'PUT',
                    data: { title:title} ,
                    success: function (data) { }
                });
            }
        });
    </script>

@stop

@extends('layouts.admin')



@section('title', 'LIST OF CATEGORIES')



@section('content')

    <div class="content_form">

        <div class="admincat_wrapper">

            <div class="admincat_wrapper__new">

                <form class="add_form" role="form" method="POST" action="{{ url('/category/store') }}">

                    {{ csrf_field() }}

                    <input type="text"  placeholder="Name" name="title">

                    <button type="submit">Create new category</button>

                </form>

            </div>



            <div class="admincat_wrapper__items">

                @if($categories->count()> 0)

                    @foreach($categories as $category)

                    <div class="item clearfix" id="item_{{$category->id}}">

                        <p>{{$category->title}}</p>

                        <input type="text" name="catname" id="category_{{$category->id}}_title">

                        <button class="saveButton" data-category_id="{{$category->id}}">Save</button>

                        <div class="buttons">
                            <button><img src="{{asset('img/Admin/edit.png')}}" alt="alt"></button>
                            <a class="popup-with-move-anim" href="#small-dialog3"><button class="deleteButton" data-category_id="{{$category->id}}"><img src="{{asset('img/Admin/delete.png')}}" alt="alt"></button></a>
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
            category_id = $(this).attr('data-category_id');
            $("#category option").attr("disabled", false);
            $("#category option[value='" + category_id + "']").attr("disabled", true);
            $("#small-dialog3 h1").text('To delete "' + $(this).parents(".item").find(">p").text() + '" , please choose a new label for jobs in this category:');
            
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
                var category_id = $(this).attr('data-category_id');
                var title  = $('#category_'+category_id+'_title').val();
                $.ajax({
                    url: '/api/category/'+ category_id,
                    type: 'PUT',
                    data: { title:title} ,
                    success: function (data) { }
                });
        });
    </script>

@stop
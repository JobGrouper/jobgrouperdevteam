@extends('layouts.main')

@section('content')

<form class="add_form" role="form" method="POST" >

                <input type="hidden" name="job_id" />

            <label class="lang_change">Language of job posting</label>
            <div class="radio_list">
                <div class="radiodiv">
                    <input name="lang" class="radio" checked type="radio" id="1">
                    <label class="buyer buy_radio" for="1">Jobgrouper main(English)</label>
                </div>
                <div class="radiodiv">
                    <input name="lang" class="radio" type="radio" id="2">
                    <label class="buyer buy_radio" for="2">English and Chinese</label>
                </div>
                <div class="radiodiv">
                    <input name="lang" class="radio" type="radio" id="3">
                    <label class="buyer buy_radio" for="3">Chinese only</label>
                </div>
            </div>
            <div class="english_card">
                <label for="title">Title (English)</label>

                <input type="text" id="title"  name="title" />

            </div>

            <div class="china_card">
                 <label for="title">Title (Chinese)</label>

                <input type="text" id="title"  name="title_ch" />
            </div>
            <input type="checkbox" id="hot" name="hot" value="1" />

            <label class="hot_label" for="hot">Hot</label>

            <div class="english_card">
                <label for="descr">Description (English)</label>

                <textarea id="descr" name="description"></textarea>

            </div>


            <div class="china_card">
                <label for="descr">Description (Chinese)</label>

                <textarea id="descr" name="description_ch"></textarea>
            </div>
            <div class="double">

                <div class="max">

                    <label for="max">max clients</label>

                    <input type="text" id="max" name="max_clients_count" value="">

                </div>

                <div class="perclient">

                    <label for="per">Payment/Client</label>

                    <input type="text" id="per" name="salary" value="">

                </div>

                <div class="salary">

                    <label for="salary">Monthly salary</label>

                    <input readonly type="text" id="salary" value="">

                </div>

            </div>

            <select id="category"  name="category_id" style="background-image: url({{asset('img/Admin/selectarrow.png')}}) !important;">


                    <option value=""></option>




            </select>

            <div class="upload clearfix">

                <div class="img_wrapper">

                    <img src="" alt="alt">
                    <input type="file" id="file">

                </div>

                <div class="change_img">
                    <label for="file">Upload</label>
                </div>
            </div>
            <div class="container123">

                        <!-- Button trigger modal -->
                        <!-- <label for="file" class="hide_btn btn btn-primary btn-lg" data-target="#modal" data-toggle="modal">
                          Launch demo modal
                        </label> -->

                      <!-- <input type="file" name="file" id="file" size="1" class="upload-file"> -->

                      <input type="hidden" name="img" id="imgCode" value="">
                      <div id="add-event">
                          <div class="here"></div>
                      </div>

                      <div id="previewwidth"></div>
                      <div id="previewheight"></div>


                      <!-- Modal -->
                      <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true"
                      aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1" style="display: none;">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="modalLabel">Crop the image</h4>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <img id="image" src="photo.png">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="docs-buttons">
                                        <div class="btn-group btn-group-crop">
                                            <button type="button" class="btn btn-primary" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: $('.cropper-face').width(), &quot;height&quot;: 500 }">
                                                <span class="docs-tooltip" data-toggle="tooltip" title="">
                                                    Done
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <input type="hidden" value="" name="image_hash" id="adminimg">
            <div class="create_buttons">
                <button type="submit" class="create">Save</button>
                <button class="create cancel">Cancel</button>
            </div>
        </form>
@endsection
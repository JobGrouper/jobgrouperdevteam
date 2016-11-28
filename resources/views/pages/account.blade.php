@extends('layouts.main')

@section('title', 'My profile')

@section('content')


<div class="profile">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <div class="profile_title">Profile</div>

                <div class="profile_info">

                    <div class="img_wrapper">

                        <img src="{{ asset($user->image_url) }}" alt="alt">
                        <input type="file" id="file">
                        <div class="change_img"><label for="file">+</label></div>

                    </div>


                <div class="social">

                    <p class="name">{{ $user['first_name'] }} {{ $user['last_name'] }}</p>
                    <div class="edittitle">
                        <input class="edit_name" type="text" value="{{ $user['first_name'] }}">
                        <input class="edit_surname" type="text" value="{{ $user['last_name'] }}">
                                <!-- <button id="save">Save</button>
                                <button class="close"><img src="{{ asset('img/Profile/cancel.png') }}" alt="alt"></button> -->
                            </div>
                            
                            <div class="social_link linkedin">
                                <img src="{{ asset('img/Profile/link.png') }}" alt="alt">
                                <span>
                                    @if($user['linkid_url'])
                                    {{ $user['linkid_url'] }}
                                    @else
                                    No information yet.
                                    @endif
                                </span>
                                <div class="editlinkedin">
                                    <img src="{{ asset('img/Profile/link.png') }}" alt="alt">
                                    <input type="text" value="{{ $user['linkid_url'] }}"><!-- <button id="linkedinchange">Save</button><button class="close"><img src="{{ asset("img/Profile/cancel.png") }}" alt="alt"></button> -->
                                </div>
                            </div>


                            <div class="social_link facebook">
                                <img src="{{ asset('img/Profile/fb.png')}}" alt="alt">
                                <span>
                                    @if($user['fb_url'])
                                    {{ $user['fb_url'] }}
                                    @else
                                    No information yet.
                                    @endif
                                </span>
                                <div class="editfacebook">
                                   <img src="{{ asset('img/Profile/fb.png')}}" alt="alt">
                                   <input type="text" value="{{ $user['fb_url'] }}"><!-- <button id="facebookchange">Save</button><button class="close"><img src="{{ asset("img/Profile/cancel.png") }}" alt="alt"></button> -->
                               </div>
                           </div>

                           <div class="social_link github">
                            <img src="{{ asset('img/Profile/github.png')}}" alt="alt">
                            <span>
                                @if($user['git_url'])
                                {{ $user['git_url'] }}
                                @else
                                No information yet.
                                @endif
                            </span>
                            <div class="editgithub">
                                <img src="{{ asset('img/Profile/github.png') }}" alt="alt">
                                <input type="text" value="{{ $user['git_url'] }}">
                            </div>
                        </div>
                        
                        <div class="savebtn_top">
                            <button id="profiletop_save">Save</button><button class="close"><img src="{{ asset("img/Profile/cancel.png") }}" alt="alt"></button>
                        </div>
                        


                    </div>

                    <div class="rating">

                        <p>Feedback Score</p>

                        <div class="stars"><div class="yellow" style="width: {{$user['rate']}}% !important;"></div></div>

                        <!-- <button>Send message</button> -->

                    </div>

                    <div class="edit_profile" title="Edit profile"><img src="{{ asset('img/Profile/edit_pencil.png') }}" alt="alt"></div>

                </div>

                <div class="container">

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

		<!--
                <div class="profile_text">

                    <h2>Paypal</h2>


                    <p class="profile_text__paypal">{{ $user['paypal_email'] }}</p>
                    <button id="paypal_add">Add / Edit Paypal email</button>
                    <div class="edit_paypal">
                        <input type="text" placeholder="Your paypal_email..."/ ><button id="paypalchange">Save</button><button class="close"><img src="{{ asset("img/Profile/cancel.png") }}" alt="alt"></button>

                    </div>




                </div>
		-->

                <div class="profile_text">

                    <h2>Description</h2>


                    <p class="profile_text__description">{{ $user['description'] }}</p>
                    <button id="descr_add">Add / Edit Description</button>
                    <div class="edit_description">
                        <textarea placeholder="Your description..."></textarea><br><button id="descrchange">Save</button><button class="close"><img src="{{ asset("img/Profile/cancel.png") }}" alt="alt"></button>

                    </div>




                </div>

                <div class="profile_portfolio">

                    <h2 class="title">Portfolio</h2>

                    <div class="block work">

                        <h3>Experience</h3>
                        <div class="work_block__wrapper">
                            @if(count($user->experience()->get()) > 0)
                            @foreach($user->experience()->get() as $experience)
                            <div class="work_block"><h2>{{$experience->title}}</h2>
                                <p><span class="fromspan">{{$experience->date_from}}</span> - <span class="tospan">{{$experience->date_to}}</span></p>
                                <p class="mixed">{{$experience->additional_info}}</p>
                                <div class="edit_profile" title="Edit job">
                                    <img src="{{asset('img/Profile/edit_pencil.png')}}" alt="alt">
                                </div>
                                <div class="work_edit" data-id="{{$experience->id}}">
                                    <input type="text" class="longinput jobtitle" placeholder="Job title">
                                    <div class="date"><input type="text" class="from" maxlength="10" readonly placeholder="From">
                                        <input type="text" class="to" readonly maxlength="10" placeholder="To">
                                    </div>
                                    <input type="text" class="longinput addinfo" placeholder="Additional information">
                                    <div class="work_edit__btn">
                                        <button class="workchange">Save</button>
                                        <button class="delete">Delete job</button>
                                        <button class="close">
                                            <img src="{{asset('img/Profile/cancel.png')}}" alt="alt">
                                        </button>
                                        <button class="close_edit">
                                            <img src="{{asset('img/Profile/cancel.png')}}" alt="alt">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>

                        <div class="one_more">
                            <button>Add job</button>
                        </div>



                    </div>

                    <div class="block">

                        <h3>Education</h3>

                        <div class="education_block__wrapper">
                           @if(count($user->education()->get()) > 0)
                           @foreach($user->education()->get() as $education)
                           <div class="education_block"><h2>{{$education->title}}</h2>
                            <p><span class="fromspan">{{$education->date_from}}</span> - <span class="tospan">{{$education->date_to}}</span></p>
                            <p class="mixed">{{$education->additional_info}}</p>
                            <div class="edit_profile" title="Edit education">
                                <img src="{{asset('img/Profile/edit_pencil.png')}}" alt="alt">
                            </div>
                            <div class="work_edit" data-id="{{$education->id}}">
                                <input type="text" class="longinput jobtitle" placeholder="Education title">
                                <div class="date"><input type="text" class="from" readonly maxlength="10" placeholder="From">
                                    <input type="text" class="to" readonly maxlength="10" placeholder="To">
                                </div>
                                <input type="text" class="longinput addinfo" placeholder="Additional information">
                                <div class="work_edit__btn">
                                    <button class="workchange">Save</button>
                                    <button class="delete">Delete education</button>
                                    <button class="close">
                                        <img src="{{asset('img/Profile/cancel.png')}}" alt="alt">
                                    </button>
                                    <button class="close_edit">
                                        <img src="{{asset('img/Profile/cancel.png')}}" alt="alt">
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>

                    <div class="one_more2">
                        <button>Add education</button>
                    </div>


                </div>

                <div class="block skill">

                    <h3>Skills</h3>

                    <div class='skill_edit'>
                        <div class='work_edit__btn'>
                          <button class='workchange'>Save</button>
                          <button class='close'><img src="{{asset('img/Profile/cancel.png')}}" alt='alt'></button>
                          <button class='close_edit'><img src="{{asset('img/Profile/cancel.png')}}" alt='alt'></button>
                      </div>
                  </div>

                  <div class="skills_block__wrapper"><p class="mixed solo">
                    @if(count($user->skills()->get()) > 0)
                    @foreach($user->skills()->get() as $skill)
                    <span data-id="{{$skill->id}}">{{$skill->title}}</span><button class='close' title='delete skill'><img src="{{asset('img/Profile/cancel.png')}}" alt='alt'></button>
                    @endforeach
                    @endif
                </p></div>

                <div class="one_more3">
                    <button>Add Skill</button>
                </div>

            </div>

            <div class="block">

                <h3>Additional Information</h3>

                <div class="additional_block__wrapper">
                    @if(count($user->additions()->get()) > 0)
                    @foreach($user->additions()->get() as $addition)
                    <div class="additional_block"><h2>{{$addition->title}}</h2>
                        <p class="mixed">{{$addition->additional_info}}</p>
                        <div class="edit_profile" title="Edit information">
                            <img src="{{asset('img/Profile/edit_pencil.png')}}" alt="alt">
                        </div>
                        <div class="work_edit" data-id="{{$addition->id}}">
                            <input type="text" class="longinput jobtitle" placeholder="Education title">
                            <input type="text" class="longinput addinfo" placeholder="Additional information">
                            <div class="work_edit__btn">
                                <button class="workchange">Save</button>
                                <button class="delete">Delete information</button>
                                <button class="close">
                                    <img src="{{asset('img/Profile/cancel.png')}}" alt="alt">
                                </button>
                                <button class="close_edit">
                                    <img src="{{asset('img/Profile/cancel.png')}}" alt="alt">
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                <div class="one_more4">
                    <button>Add information</button>
                </div>

            </div>

        </div>

    </div>

</div>

</div>

</div>

@endsection


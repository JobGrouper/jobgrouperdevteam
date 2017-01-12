@extends('layouts.main')

@section('title', $user['full_name'])

@section('content')

    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="profile_title">Profile</div>
                    <div class="profile_info">
                        <div class="img_wrapper">
                            <img src="{{ asset($user->image_url) }}" alt="alt">
                        </div>
                        <div class="social">
                            <p class="name">{{ $user['full_name'] }}</p>
                            @if($user['linkid_url'])
                                <div class="social_link"><img src="{{ asset('img/Profile/link.png') }}" alt="alt">
                                    {{ $user['linkid_url'] }}
                                </div>
                            @endif
                            @if($user['fb_url'])
                                <div class="social_link"><img src="{{ asset('img/Profile/fb.png')}}" alt="alt">
                                        {{ $user['fb_url'] }}
                                </div>
                            @endif
                            @if($user['git_url'])
                                <div class="social_link"><img src="{{ asset('img/Profile/github.png') }}" alt="alt">
                                        {{ $user['git_url'] }}
                                </div>
                            @endif
                        </div>
                        <div class="rating">
                            <p>Feedback Score</p>
                            <div class="stars"><div class="yellow" style="width: {{$user['rate']}}% !important;"></div></div>
                            <a href="/messages/{{ $user['id'] }}"><button>Send message</button></a>
                        </div>
                    </div>
                    <div class="profile_text">
                        @if($user['description'])
                            <h2>Description</h2>
                            <p>{{ $user['description'] }}</p>
                        @endif
                    </div>
                    <div class="profile_portfolio">
                        <h2 class="title">Portfolio</h2>
                        <div class="block">
                            <h3>Experience</h3>
                            @if(count($user->experience()->get()) > 0)
                                @foreach($user->experience()->get() as $experience)
                                    <h2>{{$experience->title}}</h2>

					<p><span class="fromspan">{{$experience->date_from}}</span> - 
					@if($experience->date_to_present == 1)
					  <span class="tospan">present</span>
					@else
					  <span class="tospan">{{$experience->date_to}}</span>
					@endif
					</p>

                                    <p class="mixed">{{$experience->additional_info}}</p>
                                @endforeach
                            @else
                                No information yet.
                                {{--{{$user->full_name}} has no experience.--}}
                            @endif
                        </div>
                        <div class="block">
                            <h3>Education</h3>
                            @if(count($user->education()->get()) > 0)
                                @foreach($user->education()->get() as $education)
                                    <h2>{{$education->title}}</h2>

				    <p><span class="fromspan">{{$education->date_from}}</span> - 
					@if($education->date_to_present == 1)
					<span class="tospan">present</span>
					@else
					<span class="tospan">{{$education->date_to}}</span>
					@endif
				    </p>

                                    <p class="mixed">{{$education->additional_info}}</p>
                                @endforeach
                            @else
                                No information yet.
                            @endif
                        </div>
                        <div class="block">
                            <h3>Skills</h3>
                            @if(count($user->skills()->get()) > 0)
                                <p class="mixed solo">
                                @foreach($user->skills()->get() as $skill)
                                    <span data-id="{{$skill->id}}">{{$skill->title}}</span>
                                @endforeach
                                </p>
                            @else
                                No information yet.
                            @endif
                        </div>
                        <div class="block">
                            <h3>Additional Information</h3>
                            @if(count($user->additions()->get()) > 0)
                                @foreach($user->additions()->get() as $addition)
                                <h2>{{$addition->title}}</h2>
                                <p class="last">{{$addition->additional_info}}</p>
                                @endforeach
                            @else
                                No information yet.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@extends('layouts.main')



@section('title', 'Thank You!')

@section('content')

    <section class="success">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="success_text">
                        <img src="{{ asset('img/Success/photo.png') }}" alt="alt">
                        <p>THANKS FOR YOUR COOPERATION</p>
			<p>You will receive an email soon updating you on your verification status.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop

@extends('layouts.main')

@section('title', 'Password Change Successful!')

@section('content')

    <section class="success">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="success_text">
                        <img src="{{ asset('img/Success/photo.png') }}" alt="alt">
                        <p>YOUR PASSWORD HAS BEEN CHANGED SUCCESSFULLY</p>
                        <a href="{{ url('/account') }}"><button>Back to your account</button></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop

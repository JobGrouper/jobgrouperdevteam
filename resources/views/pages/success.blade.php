@extends('layouts.main')



@section('title', 'Success!')



@section('content')

    <section class="success">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="success_text">
                        <img src="{{ asset('img/Success/photo.png') }}" alt="alt">
                        <p>YOU HAVE SUCCESSFULLY SIGNED UP, GO TO YOUR INBOX TO VERIFY YOUR ACCOUNT</p>
                        {{--<a href="{{ $emailUrl }}"><button>Open email</button></a>--}}
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
@extends('layouts.main')

<!-- Main Content -->
@section('content')
<div class="forgotpass">
    <div class="forgotpass_form forgot_only">
        <form role="form" method="POST" action="{{ url('/password/email') }}">
            {{ csrf_field() }}
            <h2>Forgot Password</h2>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @else
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
                <div class="invalid_login"></div>
                <button>Restore</button>
            @endif
        </form>
    </div>
</div>

{{--<div class="forgotpass">--}}
    {{--<div class="forgotpass_form">--}}
        {{--<form role="form" method="POST" action="{{ url('/password/email') }}">--}}
            {{--{{ csrf_field() }}--}}
            {{--<h2>Forgot Password</h2>--}}
            {{--@if (session('status'))--}}
                {{--<div class="alert alert-success">--}}
                    {{--{{ session('status') }}--}}
                {{--</div>--}}
            {{--@else--}}
                {{--<label for="email">Email</label>--}}
                {{--<input type="email" id="email" name="email" value="{{ old('email') }}">--}}
                {{--<div class="invalid_login"></div>--}}
                {{--<button>Log In</button>--}}
            {{--@endif--}}
        {{--</form>--}}
    {{--</div>--}}
{{--</div>--}}
@endsection

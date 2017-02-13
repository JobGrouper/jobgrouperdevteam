@extends('layouts.admin')

@section('title', 'Adjust Buyers')

@section('content')

	@include('partials.buyer-adjustment-form', ['requested' => false])

@stop

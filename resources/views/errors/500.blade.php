@extends('errors::base')

@section('title', __('Server Error'))
@section('code', 'Server Error')
@section('message', __('Oops, something went wrong on our end. Our team is working to fix it. Please try again later.'))
@section('image', url('assets/media/auth/error-500.svg'))
@extends('errors::base')

@section('title', __('Too Many Requests'))
@section('code', 'Too Many Requests')
@section('message', __('We’re experiencing a high number of requests from your IP address. Please wait a moment and try
again.'))
@section('image', url('assets/media/auth/error-429.svg'))
@extends('errors::base')

@section('title', __('Service Unavailable'))
@section('code', 'Service Unavailable')
@section('message', __('We’re currently undergoing maintenance. Please check back soon.'))
@section('image', url('assets/media/auth/error-503.svg'))
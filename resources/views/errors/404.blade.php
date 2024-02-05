@extends('errors::base')

@section('title', __('Not Found'))
@section('code', 'Oops! Why you’re here?')
@section('message', __('We apologize for the inconvenience. It looks like you’re try to access a page that either has
been deleted or never existed.'))
@section('image', url('assets/media/auth/error-404.svg'))
@extends('errors::base')

@section('title', __('Page Expired'))
@section('code', 'Page Expired')
@section('message', __('The page has expired due to inactivity. Please refresh and try again or navigate back to
previous page.'))
@section('image', url('assets/media/auth/error-419.svg'))
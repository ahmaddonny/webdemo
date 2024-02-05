@extends('errors::base')

@section('title', __('Unauthorized'))
@section('code', 'Oops! Access Denied')
@section('message', __('We apologize for the inconvenience. It seems you don’t have the necessary permissions to access
this page. Please make sure you’re logged in with the appropriate credentials or contact the administrator for
assistance.'))
@section('image', url('assets/media/auth/error-401.svg'))
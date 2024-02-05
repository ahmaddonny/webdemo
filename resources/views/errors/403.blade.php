@extends('errors::base')

@section('title', __('Forbidden'))
@section('code', 'Forbidden')
@section('message', __('Oops, it seems you don’t have permission to view this page. If you believe this is a mistake,
please contact the site administrator.'))
@section('image', url('assets/media/auth/error-403.svg'))
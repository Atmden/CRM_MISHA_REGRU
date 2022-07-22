@extends('template.index')
@section('title')
    Восстановление пароля
@endsection
@section('content')
    <reset-password-page
        email="{{$user->email}}"
        token="{{$token}}"
    ></reset-password-page>
@endsection

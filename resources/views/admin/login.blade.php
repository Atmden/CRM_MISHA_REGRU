{{--@extends('template.index')--}}
{{--@section('title'){{ $content->title }}@stop--}}
{{--@section('content')--}}
    <section class="title-block inside-title-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    {{--                    <h1>{{ $content->page_name }}</h1>--}}
                </div>
            </div>
        </div>
    </section>
    <section class="form-page content-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <form method="POST" action="{{ route('adminpostlogin') }}" class="form form-auth">
                        {{ csrf_field() }}
                        <h3>Авторизация</h3>
                        <input type="text"  name="email" placeholder="Email"  class="input" required="required">
                        <input type="password"  name="password" class="input" placeholder="Пароль" required="required">
                        <button class="btn btn-blue btn-login">Войти</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
{{--@stop--}}

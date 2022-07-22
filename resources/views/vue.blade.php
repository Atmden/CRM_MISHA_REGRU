<!DOCTYPE html>
<html lang="en">
<head>
    <title>Title</title>
    <meta charset="utf-8">
    <!-- <base href="/"> -->

    <title></title>
    <meta name="description" content="">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Template Basic Images Start -->
    <meta property="og:image" content="path/to/image.jpg">
    <link rel="icon" href="img/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon-180x180.png">
    <!-- Template Basic Images End -->

    <!-- Custom Browsers Color Start -->
    <meta name="theme-color" content="#354770">
    <!-- Custom Browsers Color End -->
    <link rel="stylesheet" href="{{ mix('css/vue.css') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

</head>
<body>
<div id="app">
    <!--<auth-page></auth-page> Страница авторизации-->
    <!--<acc-list-page></acc-list-page> Список аккаунтов-->
    <content-plan-page></content-plan-page>
{{--    <user-profile-page></user-profile-page>--}}
</div>
</body>

<script src='/js/tinymce/tinymce.min.js'></script>
<script src={{ mix('js/app.js') }}></script>

</html>



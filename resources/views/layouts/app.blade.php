<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/trix/0.11.4/trix.css" rel="stylesheet">
    @yield('header')
    <script>
        window.App = {!! json_encode([
            'csrfToken' => csrf_token(),
            'user' => Auth::user(),
            'signIn' => Auth::check(),
            'signedIn'=>Auth::check(),
        ]) !!};
    </script>

    <style>
        body{ padding-bottom: 100px; }
        .level { display: flex;align-items: center; }
        .flex { flex: 1 }
        .mr-1 {margin-right: 1em;}
        .ml-a { margin-left: auto; }
        .level-item { margin-right: 1em; }
        [v-cloak] { display: none; }
    </style>
</head>
<body>
<div id="app">
    @include('layouts.nav')

    @yield('content')

    <flash message="{{ session('flash') }}"></flash>
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>

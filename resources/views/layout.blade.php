<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ url('css/app.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-sm bg-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/login') }}">Login</a>
                    </li>
                </ul>
            </nav>
        </header>
        <div class="content">
            @if(!empty($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
            @endif
            @yield('content')
        </div>
    </body>
</html>
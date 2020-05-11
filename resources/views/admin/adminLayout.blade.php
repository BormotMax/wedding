<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>admin cabinet</title>
        <link rel="stylesheet" href="{{ url('css/app.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-sm bg-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/admin/users') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/admin/files') }}">Files</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/exit') }}">Exit</a>
                    </li>
                </ul>
            </nav>
        </header>
        <div class="content" id="app">
            @if(!empty($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
            @endif
            @yield('content')
        </div>
        <script src="{{asset('js/app.js')}}">
        </script>
    </body>
</html>
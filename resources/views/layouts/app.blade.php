<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Big Butterfly Month') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap-colors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css">

    @yield('style')
</head>
<body>
    <div id="app" class="table-secondary">
        <nav class="navbar navbar-expand-md navbar-dark bg-vermillion-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Big Butterfly Month
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                            <li class="nav-item dropdown">
                                <a id="analysisDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Analysis <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="analysisDropdown">
                                    <a class="dropdown-item" href="{{ url('map') }}">Map</a>
                                    <a class="dropdown-item" href="{{ url('analysis') }}">Analysis</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/map') }}">Map</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="CountDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Butterfly Count <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="CountDropdown">
                                    <a class="dropdown-item" href="{{ url('butterfly_count') }}">Index</a>
                                    <a class="dropdown-item" href="{{ url('butterfly_count/import') }}">Import</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Species <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ url('species') }}">Index</a>
                                    <a class="dropdown-item" href="{{ url('species/id_quality') }}">ID Quality</a>
                                    <a class="dropdown-item" href="{{ url('species/correct') }}">Correct Columns</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/species_names') }}">Species Names</a>
                            </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>
    @yield('script')
</body>
</html>

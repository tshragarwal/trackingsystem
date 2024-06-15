<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tracking System') }}</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.3/bootstrap-table.min.js"></script>
    @php
        $siteURLWithoutScheme = preg_replace('/^https?:\/\//', '', request()->root());
    @endphp
    @if (in_array($siteURLWithoutScheme, [
        env('SEARCHOSS_ADMIN_APP_DOMAIN'), 
        env('SEARCHOSS_PUBLISHER_APP_DOMAIN'), 
        env('SEARCHOSS_API_DOMAIN'), 
        env('TRCKWINNERS_DOMAIN') ]))
        @php
            $siteLogo = "/logo.jpeg";
        @endphp
    <link rel="icon" href="https://searchoss.com/favicon.ico" sizes="192x192" />
    @elseif (in_array($siteURLWithoutScheme, [
        env('RNMATRIKS_ADMIN_APP_DOMAIN'), 
        env('RNMATRIKS_PUBLISHER_APP_DOMAIN'), 
        env('RNMATRIKS_API_DOMAIN'), 
        env('ASKK2KNOW_DOMAIN') ]))
    @php
        $sitelogo = "/rnmatriks-logo.png";
    @endphp
    <link rel="icon" href="https://searchoss.com/rnmatriks-favicon.ico" sizes="192x192" /> 
    @endif
    <script>
        jQuery(document).ready(function($) {
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
        })
    </script>
</head>

<body>
    <div id="guest2">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm col-sm-12">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <div class="topSection"> 
                        <a class="logoBox" href="{{ route('login') }}"><img class="img-responsive"
                                src="{{ $sitelogo }}" alt="" /></a> 
                    </div>
                </div>
            </div>
        </nav>

        <div id="wrapper" style="margin-top: 40px;">
            <!-- Content Body -->
            <div class="dataSection"> @yield('content') </div>
        </div>
    </div>
</body>

</html>

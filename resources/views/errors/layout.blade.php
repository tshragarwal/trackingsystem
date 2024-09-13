<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        @php
        $siteURLWithoutScheme = preg_replace('/^https?:\/\//', '', request()->root());
        @endphp
        @if (in_array($siteURLWithoutScheme, [
            env('SEARCHOSS_ADMIN_APP_DOMAIN'), 
            env('SEARCHOSS_PUBLISHER_APP_DOMAIN'), 
            env('SEARCHOSS_API_DOMAIN'), 
            env('TRCKWINNERS_DOMAIN') ]))
        <link rel="icon" href="https://searchoss.com/favicon.ico" sizes="192x192" />
        @elseif (in_array($siteURLWithoutScheme, [
            env('RNMATRIKS_ADMIN_APP_DOMAIN'), 
            env('RNMATRIKS_PUBLISHER_APP_DOMAIN'), 
            env('RNMATRIKS_API_DOMAIN'), 
            env('ASKK2KNOW_DOMAIN') ]))
        <link rel="icon" href="https://searchoss.com/rnmatriks-favicon.ico" sizes="192x192" /> 
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 36px;
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title">
                    @yield('message')
                </div>
            </div>
        </div>
    </body>
</html>

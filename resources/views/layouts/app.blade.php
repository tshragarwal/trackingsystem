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

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    
    <script>
        jQuery(document).ready(function($){
            $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            });
        })
        </script>
        <style>
            body {
                overflow-x: hidden;
            }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            -webkit-transition: margin .25s ease-out;
            -moz-transition: margin .25s ease-out;
            -o-transition: margin .25s ease-out;
            transition: margin .25s ease-out;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
        }

        #sidebar-wrapper .list-group {
        width: 15rem;
        }

    

        #wrapper.toggled #sidebar-wrapper {
        margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

         

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }
        </style>   
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('login')}}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
<!--                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>-->
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/logout"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="/logout" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        
        
        <div class="d-flex" id="wrapper">

            @if(Auth::guard('web')->check())
            
            <!-- Sidebar -->
            <div class="bg-light border-right" id="sidebar-wrapper">
                <div class="list-group list-group-flush">
                    @if( Auth::guard('web')->user()->user_type == "admin")
                        <a href="/advertiser/list" class="list-group-item list-group-item-action bg-light">Advertiser</a>
                        <a href="/campaign/list" class="list-group-item list-group-item-action bg-light">Campaign</a>
                        <a href="/publisher/list" class="list-group-item list-group-item-action bg-light">Publisher</a>
                        <a href="/publisher/job/list" class="list-group-item list-group-item-action bg-light">Publisher Job</a>
                    @endif
                    <a href="javascript:void(0)"  style="border: 1px rgba(0,0,0,.125);" class="parentReport list-group-item list-group-item-action bg-light">
                        Report<br/>
                    </a>
                    <ul class="childReport " style="border: 1px solid rgba(0,0,0,.125); display: none">
				    <li><a class="" href="{{route('report.list')}}">N2S Report</a></li>
				    <li><a class="" href="{{route('report.typein_list')}}">Typein Report</a></li>
				</ul>
                   

                </div>
            </div>
             @endif
            <!--<button style="height:70px" class="btn btn-primary" id="menu-toggle">Toggle Menu</button>-->

            
             <!-- Content Body -->
            <div class="container-fluid" style='margin-top: 20px;'>
                @yield('content')
            </div>
        
       

        </div>
       
    </div>
    <script>
        $('.parentReport').on('click', function(){
            $('.childReport').css("display", "block");
        });
        </script>
</body>
</html>

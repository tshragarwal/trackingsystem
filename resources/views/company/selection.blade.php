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
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm col-sm-12">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <div class="topSection">  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <!--<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}"><span class="navbar-toggler-icon"></span> </button>-->
                    <div class="collapse navbar-collapse " id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto"></ul>
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('login') }}">{{ __('Login') }}</a> </li>
                                @endif
                                @if (Route::has('register'))
                                    <!-- <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>-->
                                @endif
                            @else
                                <li class="nav-item dropdown"> 
                                  <a id="navbarDropdown" class="nav-link dropdown-toggle"
                                        href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" v-pre> {{ Auth::user()->name }} </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"> 
                                      <a
                                        class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }} </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="d-flex" id="wrapper">
                

            <!-- Content Body -->
            <div class="dataSection" style="width: 100%"> 
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <style>
                    .table-responsive {
                        max-width: 100%;
                        overflow-x: auto;
                    }
                </style>
                <link href="{{ asset('css/tablefixed.css') }}" rel="stylesheet">
                <div class="container">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Company</a></li>

                        </ol>
                    </nav>
                    <div class="card card-body col-sm-12" style="margin-bottom: 20px; border:none">
                            <div class="form-group ">
                                <div class='row'>
                                    <label class="sr-only">
                                        <lable> Choose company </lable>
                                    </label>
                                <select name="company" class="form-control" style="width: 50%; margin:auto;" onchange="changeCompany(this.value)">
                                    <option value="">--Choose Company--</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                    
                                </div>
                            </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <script>
        function changeCompany(val) {
            if (val == "") {
                return false;
            }

            window.location.href = "/" + val + "/advertiser"
        }
    </script>
</body>

</html>



    




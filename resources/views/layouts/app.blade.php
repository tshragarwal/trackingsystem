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
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.3/bootstrap-table.min.js"></script>    
    <script>
        jQuery(document).ready(function($){
            $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            });
        })
    </script>
    <style>
        body{overflow-x:hidden}
        img{max-width:100%;max-height:100%}
        form lable{margin-bottom:2px;display:block;font-size:13px}
        form .dropdown{padding:0;margin:0}
        form .dropdown-toggle{background:#fff;border:1px solid #ced4da}
        form .dropdown-toggle:hover{background:#fff;border:1px solid #ced4da}
        form .form-control{margin-bottom:10px!important;font-size:14px;position:relative}
        form .form-control i{position:absolute;right:9px;top:9px}
        form .filter-option-inner-inner{font-size:14px}
        .breadcrumb{background:#e8f0fd}
        .breadcrumb a{color:#1565c0}
        .navbar{padding:0 1rem;position:sticky;top:0;z-index:9}
        .navbar .row{width:calc(100% + 30px)}
        .topSection{display:flex;align-items:center;gap:5px}
        .topSection .logoBox{padding-right:15px;border-right:1px solid rgba(0,0,0,.125);display:block;width:calc(15rem - 15px)}
        .topSection em{padding:10px;cursor:pointer}
        #sidebar-wrapper{min-height:100vh;margin-left:-15rem;-webkit-transition:margin .25s ease-out;-moz-transition:margin .25s ease-out;-o-transition:margin .25s ease-out;transition:margin .25s ease-out;background:#fff!important;margin-left:0;position:sticky;top:55px;height:calc(100vh - 55px);min-height:inherit!important}
        #wrapper.toggled #sidebar-wrapper{margin-left:0}
        #sidebar-wrapper .sidebar-heading{padding:.875rem 1.25rem;font-size:1.2rem}
        #sidebar-wrapper .list-group{width:15rem}
        #sidebar-wrapper .list-group ul{padding:0;margin:0;display:block;list-style:none}
        #sidebar-wrapper .list-group ul li{padding:0;margin:0;border-bottom:1px solid rgba(0,0,0,.125);position:relative}
        #sidebar-wrapper .list-group ul li:first-child{border-top:1px solid rgba(0,0,0,.125)}
        #sidebar-wrapper .list-group ul li::before{content:'';background:#e8f0fd;width:0;height:100%;border-radius:0 50px 50px 0;position:absolute;top:0;left:0;display:block;transition:.5s}
        #sidebar-wrapper .list-group ul li.dropdown::before{display:none}
        #sidebar-wrapper .list-group ul li.dropdown::after{content:'\f107';width:10px;height:10px;position:absolute;top:12px;right:12px;display:block;transition:.5s;font:normal normal normal 14px/1 FontAwesome}
        #sidebar-wrapper .list-group ul li a,#sidebar-wrapper .list-group ul li span{padding:.55rem 1.25rem;margin:0;border:0;background:none!important;font-size:.9rem;color:#495057;display:flex;gap:8px;align-items:center;position:relative;transition:.5s}
        #sidebar-wrapper .list-group ul li a em,#sidebar-wrapper .list-group ul li span em{width:18px}
        #sidebar-wrapper .list-group ul li ul li{border:0!important}
        #sidebar-wrapper .list-group ul li ul li a{padding-left:2.9rem;display:block;color:#495057bf;font-size:.7rem}
        #sidebar-wrapper .list-group ul li:hover::before{width:98%;transition:.5s}
        #sidebar-wrapper .list-group ul li:hover a{background:none!important;box-shadow:none!important;text-decoration:none;color:#1565c0;transition:.5s}
        .table-responsive{border-left:1px solid #dee2e6;border-right:1px solid #dee2e6;max-width:100%;overflow-x:auto}
        .table th{font-size:.8em;white-space:nowrap}
        .table td{font-size:.8em}
        .table td:last-child{white-space:nowrap}
        .table td a{color:#1565c0}
        .d-flex{position:relative}
        .d-flex #menu-toggle{padding:5px;position:absolute;bottom:10px;right:10px;border-radius:50px;background:#f3f3f3;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:22px;cursor:pointer;transition:.5s;box-shadow: 0 1px 8px #adadad;}
        .d-flex .dataSection{margin-top:20px;width:calc(100% - 15rem)}
        .d-flex .dataSection .container{max-width:100%}
        .d-flex.toggled .dataSection{width:100%}
        .d-flex.toggled #menu-toggle{right:-35px;border-radius:0 50px 50px 0;transition:.5s;}
        .card{border-radius:15px;overflow:hidden;}
        .card .card-header{background:#e8f0fd;font-weight:600;}
        @media (min-width: 768px) {
        #sidebar-wrapper{margin-left:0}
        #wrapper.toggled #sidebar-wrapper{margin-left:-15rem}
        }
        @media screen and (max-width:540px) {
        .d-flex .dataSection{width:100%}
        #wrapper #sidebar-wrapper{margin-left:-15rem;}
        .d-flex.toggled #menu-toggle{right:10px;border-radius:50px;transition:.5s;}
        .d-flex #menu-toggle{right:-35px;border-radius:0 50px 50px 0;transition:.5s;}
        }
    </style>   
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm col-sm-12">
          <div class="row">
              <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                <div class="topSection"> <a class="logoBox" href="{{ url('/') }}"><img class="img-responsive" src="/logo.jpeg" alt=""/></a>  </div>
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
                    <li class="nav-item"> <a class="nav-link" href="{{route('login')}}">{{ __('Login') }}</a> </li>
                    @endif
                    @if (Route::has('register')) 
                    <!-- <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>--> 
                    @endif
                    @else
                    <li class="nav-item dropdown"> <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre> {{ Auth::user()->name }} </a>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"> <a class="dropdown-item" href="{{route('logout')}}t" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> {{ __('Logout') }} </a>
                        <form id="logout-form" action="{{route('logout')}}" method="POST" class="d-none">
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
            
        <div class="d-flex" id="wrapper"> @if(Auth::guard('web')->check()) 
          <!-- Sidebar -->
          <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="list-group list-group-flush"> @if( Auth::guard('web')->user()->user_type == "admin")
              <ul>
                <li><a href="{{route('advertiser.list')}}" class="list-group-item list-group-item-action bg-light"><em class="fa  fa-desktop"></em> Advertiser</a></li>
                <li><a href="{{route('campaign.list')}}" class="list-group-item list-group-item-action bg-light"><em class="fa fa-calendar"></em>Campaign</a></li>
                <li><a href="{{route('publisher.list')}}" class="list-group-item list-group-item-action bg-light"><em class="fa fa-address-card-o"></em>Publisher</a></li>
                <li><a href="{{route('publisher.job.list')}}" class="list-group-item list-group-item-action bg-light"><em class="fa fa-list-alt"></em>Publisher Job</a></li>
                <li class="dropdown"><span class="list-group-item list-group-item-action bg-light"><em class="fa fa-file-excel-o"></em>Report</span>
                  <ul class="childReport">
                    <li><a href="{{route('report.list')}}">N2S Report</a></li>
                    <li><a href="{{route('report.typein_list')}}">Typein Report</a></li>
                    <li><a href="{{route('report.typein_list', ['start_date'=> date('Y-m-d') , 'end_date'=> date('Y-m-d')])}}">Keyword Traffic Report</a></li>
                  </ul>
                </li>
                @else
                <li class="dropdown"><span class="list-group-item list-group-item-action bg-light"><em class="fa fa-outdent"></em>Report</span>
                  <ul class="childReport">
                    <li><a href="{{route('report.list')}}">N2S Report</a></li>
                    <li><a href="{{route('report.typein_list')}}">Typein Report</a></li>
                  </ul>
                </li>
                <li><a href="{{route('publisher_token.token_list')}}" class="list-group-item list-group-item-action bg-light"><em class="fa fa-outdent"></em>API</a></li>
                @endif
              </ul>
            </div>
            <em id="menu-toggle" class="fa fa-angle-left"></em>
          </div>

          @endif 
          <!-- Content Body -->
          <div class="dataSection"> @yield('content') </div>
        </div>
    </div>
    <script>
        $('.parentReport').on('click', function(){
            $('.childReport').css("display", "block");
        });
    </script>
</body>
</html>

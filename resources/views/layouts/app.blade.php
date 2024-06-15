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
        env('TRCKWINNERS_DOMAIN') ]) || $companyID === 1)
    <link rel="icon" href="https://searchoss.com/favicon.ico" sizes="192x192" />
    @elseif (in_array($siteURLWithoutScheme, [
        env('RNMATRIKS_ADMIN_APP_DOMAIN'), 
        env('RNMATRIKS_PUBLISHER_APP_DOMAIN'), 
        env('RNMATRIKS_API_DOMAIN'), 
        env('ASKK2KNOW_DOMAIN') ]) || $companyID === 2)
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
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm col-sm-12">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <div class="topSection"> 
                        <a class="logoBox" href="{{ route('advertiser.list', ['company_id' => $companyID]) }}"><img class="img-responsive"
                                src="{{ $companyLogo }}" alt="" /></a> 
                    </div>
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
                                <li>
                                    @if (Auth::user()->user_type === 'admin')
                                    <a class="btn btn-primary" href="{{ route('companySelection') }}">Change Company</a>
                                    @endif
                                </li>
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
            @if (Auth::guard('web')->check())
                <!-- Sidebar -->
                <div class="bg-light border-right" id="sidebar-wrapper">
                    <div class="list-group list-group-flush">
                        @if (Auth::guard('web')->user()->user_type == 'admin')
                            <ul>
                                <li>
                                    <a href="{{ route('advertiser.list', ['company_id' => $companyID]) }}"
                                        class="list-group-item list-group-item-action bg-light">
                                        <em class="fa  fa-desktop"></em> Advertiser
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('campaign.list', ['company_id' => $companyID]) }}"
                                        class="list-group-item list-group-item-action bg-light"><em
                                            class="fa fa-calendar"></em>Campaign</a>
                                </li>
                                <li>
                                  <a href="{{ route('publisher.list', ['company_id' => $companyID]) }}"
                                        class="list-group-item list-group-item-action bg-light"><em
                                            class="fa fa-address-card-o"></em>Publisher</a></li>

                                <li><a href="{{ route('publisherJob.list', ['company_id' => $companyID]) }}"
                                        class="list-group-item list-group-item-action bg-light"><em
                                            class="fa fa-list-alt"></em>Publisher Job</a></li>

                                <li class="dropdown"><span class="list-group-item list-group-item-action bg-light"><em
                                            class="fa fa-file-excel-o"></em>Report</span>
                                    <ul class="childReport">
                                        <li><a href="{{ route('report.list', ['company_id' => $companyID, 'type' => 'n2s']) }}">N2S Report</a></li>
                                        <li><a href="{{ route('report.list', ['company_id' => $companyID, 'type' => 'typein']) }}">Typein Report</a></li>
                                        <li><a
                                                href="{{ route('traffic.tracking_report', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d'), 'type' => 'count']) }}">Traffic
                                                Report</a></li>

                                        <!--                    <li><a href="{{ route('traffic.count_list', ['company_id' => $companyID,'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">Count Traffic Report</a></li>
                    <li><a href="{{ route('traffic.keyword_list', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">Keyword Traffic Report</a></li>
                    <li><a href="{{ route('traffic.agent_report', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">Browser Report</a></li>
                    <li><a href="{{ route('traffic.location_report', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">Location Report</a></li>
                    <li><a href="{{ route('traffic.device_report', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">Device Report</a></li>
                    <li><a href="{{ route('traffic.ip_report', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">IP Report</a></li>
                    <li><a href="{{ route('traffic.platform_report', ['company_id' => $companyID, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}">Platform Report</a></li>-->
                                    </ul>
                                </li>
                            @else
                                <li class="dropdown"><span class="list-group-item list-group-item-action bg-light"><em
                                            class="fa fa-outdent"></em>Report</span>
                                    <ul class="childReport">
                                        <li><a href="{{ route('report.list', ['company_id' => $companyID, 'type' => 'n2s']) }}">N2S Report</a></li>
                                        <li><a href="{{ route('report.list', ['company_id' => $companyID, 'type' => 'typein']) }}">Typein Report</a></li>
                                    </ul>
                                </li>
                                <li><a href="{{ route('publisher_token.token_list', ['company_id' => $companyID]) }}"
                                        class="list-group-item list-group-item-action bg-light"><em
                                            class="fa fa-outdent"></em>API</a></li>
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
        $('.parentReport').on('click', function() {
            $('.childReport').css("display", "block");
        });
    </script>
</body>

</html>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title') | Rola </title>

    <!--<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ url('assets/img/icons/apple-touch-icon-144x144.png') }}" />-->
    <!--<link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ url('assets/img/icons/apple-touch-icon-152x152.png') }}" />-->
    <link rel="shortcut icon" href="{{ url('/favico.ico') }}">
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <!--<meta name="msapplication-TileImage" content="{{ url('assets/img/icons/mstile-144x144.png') }}" />-->
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    {{-- For production, it is recommended to combine following styles into one. --}}
    {!! HTML::style('assets/css/bootstrap.min.css') !!}
    <!--{!! HTML::style('assets/css/font-awesome.min.css') !!}-->
    {!! HTML::style('assets/css/metisMenu.css') !!}
    {!! HTML::style('assets/css/sweetalert.css') !!}
    <!--{!! HTML::style('assets/css/bootstrap-social.css') !!}-->
    {!! HTML::style('assets/css/app.css') !!}
    
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            
            <div id="navbar" class="navbar-collapse">
                
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            
                            {{ Auth::user()->present()->name }}
                             <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if (config('session.driver') == 'database' && Entrust::hasRole('Administradora'))
                                <li>
                                    <a href="{{ route('profile.sessions') }}">
                                        <i class="glyphicon glyphicon-th-list"></i>
                                        @lang('app.active_sessions')
                                    </a>
                                </li>
                            @endif
                            @role(['Administradora','Coordinadora','Directora','Admin','Contralora'])
                                <li>
                                    <a href='{{url("/user/".auth()->user()->id."/edit")}}'>
                                        <i class="glyphicon glyphicon-user"></i>
                                        Perfil
                                    </a>
                                </li>
                            @endrole
                            @role('Colega')
                                <li>
                                    <a href='{{url("/user/".auth()->user()->id."/password")}}'>
                                        <i class="glyphicon glyphicon-user"></i>
                                        Cambiar Contraseña
                                    </a>
                                </li>
                            @endrole
                            <li>
                                <a href="{{ route('auth.logout') }}">
                                    <i class="glyphicon glyphicon-log-out"></i>
                                    @lang('app.logout')
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @include('partials.sidebar')

    <div id="page-wrapper" class="gray-bg">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-xs-12 col-lg-12 ">
                    <div class="ibox float-e-margins">                
                        <div class="ibox-content">
                        @yield('content')
                        </div>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>

    {{-- For production, it is recommended to combine following scripts into one. --}}
    {!! HTML::script('assets/js/jquery-2.1.4.min.js') !!}
    {!! HTML::script('assets/js/bootstrap.min.js') !!}
    {!! HTML::script('assets/js/metisMenu.min.js') !!}
    {!! HTML::script('assets/js/sweetalert.min.js') !!}
    {!! HTML::script('assets/js/delete.handler.js') !!}
    {!! HTML::script('assets/plugins/js-cookie/js.cookie.js') !!}
    <link href="{{url('/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{url('/js/plugins/dataTables/datatables.min.js')}}"></script>
    
    <script type="text/javascript">
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    </script>
    @if(!Request::is('feriados/calendar') || !Request::is('user/create'))
        {!! HTML::script('assets/js/jsvalidation/js/jsvalidation.js') !!}
        {!! HTML::script('assets/js/as/app.js') !!}
    @endif
    
    
    @yield('scripts')
</body>
</html>

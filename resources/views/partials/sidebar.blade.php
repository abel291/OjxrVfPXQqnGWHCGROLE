<div class="navbar-default sidebar" role="navigation">
    <a href="#" id="sidebar-toggle" class="btn">
        <i class="navbar-icon glyphicon glyphicon-menu-hamburger icon"></i>
    </a>
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">

            <li class="sidebar-avatar">
                <div class="dropdown nav-header">                    
                    <div class="name"><strong>{{auth()->user()->first_name}} {{auth()->user()->last_name}}</strong></div>
                    <span class="text-muted text-xs block">{{auth()->user()->email}} </span> </a>
                </div>
            </li>
           
            <li class="{{ Request::is('/') ? 'active open' : ''  }}">
                <a href="{{ route('dashboard') }}" class="{{ Request::is('/') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-dashboard"></i> @lang('app.dashboard')
                </a>
            </li>
           
            
            @role(['Directora','Coordinadora','Administradora','Admin','Contralora'])
            <li class="{{ Request::is('user*') ? 'active open' : ''  }}">
                <a href="{{ route('user.list') }}" class="{{ Request::is('user*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-user"></i> Colegas
                </a>
            </li>           
            @endrole
            
            
            
            @role(['Directora','Coordinadora','Administradora','Admin','Contralora'])
            <li class="{{ Request::is('planilla*') ? 'active open' : ''  }}">
               <a href="{{ route('planilla.normal') }}"  class="{{ Request::is('planilla*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-file"></i> Planillas
                </a>
            </li>
            @endrole

            @role(['Administradora','Admin', 'Directora', 'Coordinadora','Contralora'])
            <li class="{{ Request::is('feriados*') ? 'active open' : ''  }}">
                <a href="#" >
                    <i class="glyphicon glyphicon-calendar"></i> Feriados
                </a>
                <ul class="nav nav-second-level collapse">
                    <li>
                        <a href="{{ route('feriados.list') }}" class="{{ Request::is('feriados') ? 'active' : ''  }}">
                            Ingresar feriado
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('feriados.calendar') }}" class="{{ Request::is('feriados/calendar*') ? 'active' : ''  }}">
                            Calendario
                        </a>
                    </li>
                </ul>
            </li>
            @endrole

            @role(['Administradora','Coordinadora','Colega','Directora','Contralora','Admin'])
            <li class="{{ Request::is('permisos*') ? 'active open' : ''  }}">
                <a href="{{ route('permisos.list') }}" class="{{ Request::is('permisos*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-time"></i> Permisos y Ausencias
                </a>
            </li>
            <li class="{{ Request::is('vacaciones*') ? 'active open' : ''  }}">
                <a href="{{ route('vacaciones.list') }}" class="{{ Request::is('vacaciones*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-plane"></i> Vacaciones
                </a>
            </li>
            @endrole

            @role(['Directora','Coordinadora','Administradora','Admin','Contralora'])
            <li class="{{ Request::is('reportes*') ? 'active open' : ''  }}">
               <a href="{{ route('reportes') }}"  class="{{ Request::is('reportes*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-list-alt"></i> Reportes
                </a>
            </li>
            @endrole 
            
            @role(['Administradora','Admin','Directora','Coordinadora','Contralora'])
            <li class="{{ Request::is('contratos*') ? 'active open' : ''  }}">
                <a href="{{ route('contratos.list') }}" class="{{ Request::is('contratos*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-briefcase"></i> Contratos
                </a>
            </li>
            @endrole

            {{-- @role(['Administradora','Admin','Coordinadora','Contralora','Directora'])
            <li class="{{ Request::is('recepciones*') ? 'active open' : ''  }}">
                <a href="{{ route('recepciones.list') }}" class="{{ Request::is('recepciones*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-cog"></i>Recepciónes
                </a>
            </li>
            @endrole --}}
            
            @role(['Administradora','Admin','Coordinadora','Contralora'])
            <li class="{{ Request::is('ajustes*') ? 'active open' : ''  }}">
                <a href="{{ route('ajustes') }}" class="{{ Request::is('ajustes*') ? 'active' : ''  }}">
                    <i class="glyphicon glyphicon-cog"></i> Ajustes
                </a>
            </li>
            @endrole

           
            
            <hr>
            <li>
                <a href="{{ route('auth.logout') }}">
                    <i class="glyphicon glyphicon-log-out"></i>    Salir
                </a>
            </li>
            
            <!--<li class="{{ Request::is('/vacaciones') ? 'active open' : ''  }}">
                <a href="{{ route('dashboard') }}" class="{{ Request::is('/vacaciones') ? 'active' : ''  }}">
                    <i class="fa fa-plane fa-fw"></i> Vacaciones
                </a>
            </li>            

            @permission('users.activity')
            <li class="{{ Request::is('activity*') ? 'active open' : ''  }}">
                <a href="{{ route('activity.index') }}" class="{{ Request::is('activity*') ? 'active' : ''  }}">
                    <i class="fa fa-list-alt fa-fw"></i> @lang('app.activity_log')
                </a>
            </li>
            @endpermission

            @permission(['roles.manage', 'permissions.manage'])
            <li class="{{ Request::is('role*') || Request::is('permission*') ? 'active open' : ''  }}">
                <a href="#">
                    <i class="fa fa-user fa-fw"></i>
                    @lang('app.roles_and_permissions')
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    @permission('roles.manage')
                    <li>
                        <a href="{{ route('role.index') }}" class="{{ Request::is('role*') ? 'active' : ''  }}">
                            @lang('app.roles')
                        </a>
                    </li>
                    @endpermission
                    @permission('permissions.manage')
                    <li>
                        <a href="{{ route('permission.index') }}"
                        class="{{ Request::is('permission*') ? 'active' : ''  }}">@lang('app.permissions')</a>
                    </li>
                    @endpermission
                </ul>
            </li>
            @endpermission

            @permission(['settings.general', 'settings.auth', 'settings.notifications'])
            <li class="{{ Request::is('settings*') ? 'active open' : ''  }}">
                <a href="#">
                    <i class="fa fa-gear fa-fw"></i> @lang('app.settings')
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    @permission('settings.general')
                    <li>
	                        <a href="{{ route('settings.general') }}"
	                        class="{{ Request::is('settings') ? 'active' : ''  }}">
	                        @lang('app.general')
	                    </a>
	                </li>
	                @endpermission
	                @permission('settings.auth')
	                <li>
	                    <a href="{{ route('settings.auth') }}"
	                    class="{{ Request::is('settings/auth*') ? 'active' : ''  }}">
	                    @lang('app.auth_and_registration')
		                </a>
		            </li>
		            @endpermission
		            @permission('settings.notifications')
		            <li>
		                <a href="{{ route('settings.notifications') }}"
		                class="{{ Request::is('settings/notifications*') ? 'active' : ''  }}">
		                @lang('app.notifications')
		            	</a>
			        </li>
			        @endpermission
			    </ul>
			</li>
			@endpermission-->
</ul>
</div>
<!-- /.sidebar-collapse -->
</div>
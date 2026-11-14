<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree" data-accordion="true">
            <li class="header">MENÚ PRINCIPAL</li>

            <li class="{{ request()->is('admin') ? 'active' : '' }}">
                <a href="{{ url('/admin') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.vision-lab.*') ? 'active' : '' }}">
                <a href="{{ route('admin.vision-lab.index') }}">
                    <i class="fa fa-eye"></i>
                    <span>Vision Lab</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.digital-twin') ? 'active' : '' }}">
                <a href="{{ route('admin.digital-twin') }}">
                    <i class="fa fa-user-md"></i>
                    <span>Gemelo Digital</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.mission-simulator') ? 'active' : '' }}">
                <a href="{{ route('admin.mission-simulator') }}">
                    <i class="fa fa-line-chart"></i>
                    <span>Simulador SANS</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.nasa-data*') ? 'active' : '' }}">
                <a href="{{ route('admin.nasa-data') }}">
                    <i class="fa fa-database"></i>
                    <span>Datos NASA</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.research-intelligence*') ? 'active' : '' }}">
                <a href="{{ route('admin.research-intelligence') }}">
                    <i class="fa fa-graduation-cap"></i>
                    <span>Inteligencia Científica</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('admin.demo-theater*') ? 'active' : '' }}">
                <a href="{{ route('admin.demo-theater') }}">
                    <i class="fa fa-play-circle"></i>
                    <span>Demo Theater (Jueces)</span>
                </a>
            </li>



            @if (auth()->user()->can('users.manage') || auth()->user()->hasAnyRole(['super-admin', 'admin']))
            <li class="treeview {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.contacts.*') ? 'active menu-open' : '' }}">
                <a href="#">
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span>Administración</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right" aria-hidden="true"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @hasanyrole('super-admin|admin')
                    <li class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.contacts.index') }}">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <span>Mensajes de contacto</span>
                        </a>
                    </li>
                    @endhasanyrole
                    @can('users.manage')
                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    @endcan
                    @role('super-admin')
                    <li class="{{ request()->routeIs('admin.automations*') ? 'active' : '' }}">
                        <a href="{{ route('admin.automations') }}">
                            <i class="fa fa-cogs" aria-hidden="true"></i>
                            <span>Centro de Automatizaciones</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}">
                            <i class="fa fa-shield" aria-hidden="true"></i>
                            <span>Roles y permisos</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.maintenance*') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance') }}">
                            <i class="fa fa-terminal" aria-hidden="true"></i>
                            <span>Consola de comandos</span>
                        </a>
                    </li>
                    @endrole
                </ul>
            </li>
            @endif
        </ul>
    </section>
</aside>

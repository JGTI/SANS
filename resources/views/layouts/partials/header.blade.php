<header class="main-header">
    <a href="{{ url('/admin') }}" class="logo">
        <span class="logo-mini"><b>N</b>X</span>
        <span class="logo-lg"><b>Nexus</b></span>
    </a>

    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Alternar navegación</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @auth
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="padding:8px 15px">
                        @csrf
                        <button class="btn btn-default btn-sm" type="submit">Cerrar sesión</button>
                    </form>
                </li>
                @endauth
                <li>
                    <a href="{{ url('/') }}">
                        <i class="fa fa-home"></i> Sitio
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

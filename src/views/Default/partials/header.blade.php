<!-- Header Menu -->
<header class="header">
    <a href="/" class="logo">
        LaCrud Admin
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span>
                           Full Name
                        <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-gray">
                            <img src="img/avatar3.png" class="img-circle" alt="User Image" />
                            <p>
                                Full Name
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-default btn-flat">Logout</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!-- Left Menu and page Title -->
<div class="wrapper row-offcanvas row-offcanvas-left">
    <aside class="left-side sidebar-offcanvas">
        <section class="sidebar">
            <!-- User Info -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="img/avatar3.png" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p>Hi  
                        Full Name
                    </p>
                </div>
            </div>
            <!-- Left Menu -->
            <ul class="sidebar-menu">
                @if( isset($entityNames) && is_array($entityNames) )
                    @foreach ($entityNames as $tmp)
                        <li>
                            <a href="{{ URL::route('lacrud.'.$tmp['table'].'.index') }}">
                                <i class="fa fa-table"></i> <span>{{ $tmp['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </section>
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
        <!-- Content Header (Page header) -->
        <section class="content-header clearfix">
            <h1 class="pull-left">
                {{ $title }}
                <small>{{ $subtitle }}</small>
            </h1>
            @if( $isIndex && $permission['add'] )
                <a href="{{ URL::route('lacrud.'.$entity.'.create') }}" class="btn btn-success pull-right">
                    <span class="fa fa-plus-circle"></span> {{ trans('lacrud::templates.new_register') }}
                </a>
            @endif
        </section>
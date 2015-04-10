<!-- Header-->
<header class="main-header">
    <a href="/" class="logo">
        LaCrud
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Barrit menu-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User information -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="http://placephant.com/160/160" class="user-image" alt="Administrator"/>
                        <span class="hidden-xs">LaCrud Admin</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="http://placephant.com/160/160" class="img-circle" alt="User Image" />
                            <p>
                                Admin - Web Developer
                                <small>Member since Nov. 2012</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<!-- Left Sidebar Menu -->
<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="http://placephant.com/160/160" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>Admin</p>
            </div>
        </div>

        <!-- Items of menu -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
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

<!-- Page Content -->
<div class="content-wrapper">
    <!-- Content Header-->
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
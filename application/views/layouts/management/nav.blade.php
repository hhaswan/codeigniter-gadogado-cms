<header class="main-header">
    <a href="index2.html" class="logo">
        <span class="logo-mini"><b>CI</b></span>
        <span class="logo-lg"><b>Code</b>Igniter</span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><span class="sr-only">Toggle navigation</span></a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning">10</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="header"><strong>Notifikasi</strong></li>
                    <li>
                    <ul class="menu">
                        <li>
                            <a href="#">
                                Very long description here that may not fit into the
                                page and may cause design problems<br/>
                                <small>17 Juni 2017</small>
                            </a>
                        </li>
                    </ul>
                    </li>
                    <li class="footer"><a href="#">Lihat Semua</a></li>
                </ul>
                </li>
                <li class="user user-menu" data-toggle="tooltip" data-placement="bottom" title="Profil">
                    <a href="#">
                        <img src="{{base_url()}}adminlte/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs">Alexander Pierce</span>
                    </a>
                </li>
                <li data-toggle="tooltip" data-placement="bottom" title="Keluar">
                    <a href="{{base_url('logout')}}"><i class="fa fa-sign-out"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<header class="main-header">
    <a href="{{ base_url('mgmt') }}" class="logo">
        <span class="logo-mini"><img src="{{base_url('/adminlte/favicon.ico')}}" alt="Logo"/></span>
        <span class="logo-lg">{{ app()->name }}</span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><span class="sr-only">Toggle navigation</span></a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown notifications-menu" data-toggle="tooltip" data-placement="bottom" title="Notifikasi">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell"></i>
                        <!--<span class="label label-warning">10</span>-->
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"><strong>Notifikasi</strong></li>
                        <li>
                        <ul class="menu">
                            @if(false)
                                @foreach($a as $row)
                                <li>
                                    <a href="{{ $row->link_url }}">
                                        <b>{{ $row->judul }}</b><br/>
                                        <small>{{ \Carbon\Carbon::parse($row->tggl_post)->format('D, d M Y - H:i') }}</small>
                                    </a>
                                </li>
                                @endforeach
                            @else
                                <li class="text-center">
                                    <br/>
                                        <span class="text-bold text-gray">
                                            <i class="fa fa-bell-slash fa-3x"></i><br/><br/>
                                            Anda Tidak Memiliki Notifikasi
                                        </span>
                                    <br/><br/>                                        
                                </li>                                        
                            @endif
                        </ul>
                        </li>
                        <li class="footer"><a href="#">Lihat Semua</a></li>
                    </ul>
                </li>
                <li data-toggle="tooltip" data-placement="bottom" title="Profil">
                    <a href="{{base_url('/profile')}}">
                        <i class="fa fa-user-circle"></i>&nbsp;
                        <span class="hidden-xs">{{ session(((new MY_Controller())->admin_identifier))['full_name'] }}</span>
                    </a>
                </li>
                <li data-toggle="tooltip" data-placement="bottom" title="Keluar">
                    <a href="{{base_url('logout')}}" class="confirm-action" data-id="{{random_string('numeric', 16)}}"><i class="fa fa-sign-out"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
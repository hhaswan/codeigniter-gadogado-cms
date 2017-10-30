@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-md-3">

        <div class="box box-primary">
            <div class="box-body box-profile">
                <p class="text-center">
                    <i class="fa fa-user-circle fa-5x" data-toggle="tooltip" title="{{ $name }}"></i>
                    <h3 class="profile-username text-center">
                        {{ word_limiter($name, 2, "") }}
                        <p class="text-muted text-center small">
                            {{ $role[0]->name }}<br/><br/>
                            <span class="label label-primary">{{ ucwords($divisi) }}</span>
                        </p>
                    </h3>
                </p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b><i class="fa fa-calendar"></i> Terdaftar</b> <span class="pull-right">{{ $reg }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fa fa-envelope"></i> Kontak</b> {{ safe_mailto($email, "Email", 'class="pull-right"') }}
                    </li>
                    <li class="list-group-item">
                        <b><i class="fa fa-asterisk"></i> Status</b>
                        <span class="pull-right">
                            @if($status)
                                <label class="label label-success" data-toggle="tooltip" title="User ini masih aktif">AKTIF</label>
                            @else
                                <label class="label label-default" data-toggle="tooltip" title="User ini sudah tidak aktif">TIDAK AKTIF</label>
                            @endif
                        </span>
                    </li>
                </ul>

                <strong><i class="fa fa-sticky-note-o margin-r-5"></i> Tentang Saya</strong>
                <p class="text-muted text-justify">
                    {{ (! empty($result[0]->bio)) ? $result[0]->bio : "<em class='text-muted'>Tidak Ada Data</em>" }}
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#notification" data-toggle="tab">Notifikasi</a></li>
                <li><a href="#security" data-toggle="tab">Keamanan Akun</a></li>
                <li><a href="#settings" data-toggle="tab">Pengaturan Akun</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="notification">
                    <p class="text-justify">
                        <h4 class="text-bold">Notifikasi Anda</h4>
                        Daftar di bawah ini merupakan notifikasi / pemberitahuan lengkap Anda.
                    </p>
                    <br/>
                </div>

                <div class="tab-pane" id="security">
                    @include('profile.security')
                </div>

                <div class="tab-pane" id="settings">
                    @include('profile.settings')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
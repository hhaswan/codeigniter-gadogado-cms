@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-title pull-left btn-group">
                    <a href="{{ str_replace('/create', '', base_url(uri_string().'/create')) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> <span class="hidden-xs"> Management Data</span>
                    </a>
                </div>
                <div class="box-title pull-right btn-group">
                    @if(isset($links))
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-ellipsis-v"></i>&nbsp;<span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            @foreach($links as $row)
                                <li>{{ $row }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
                <div class="box-body">
                    <div class="col-lg-7">
                        @if(flash('MSG_ERROR'))
                            <div class="text-danger text-center">{{flash('MSG_ERROR')}}</div>
                        @endif
                        <form method="post" action="{{ base_url(uri_string()) }}">
                            <div class="form-group has-feedback">
                                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
                                <span class="fa fa-user form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                <span class="fa fa-envelope form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="password">Password <span class="text-danger">*</span></label>            
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <span class="fa fa-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
                                <span class="fa fa-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                {{ $role }}
                            </div>
                            <div class="form-group">
                                <label for="role">Status User <span class="text-danger">*</span></label>
                                <select name="status" class="form-control selectpicker" required>
                                    <option value="1">AKTIF</option>
                                    <option value="2">PENDING</option>
                                    <option value="0">NON-AKTIF</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role">Konfirmasi Email? <small>(email akan dikirimkan ke user ini)</small></label>
                                <select name="confirm" class="form-control selectpicker" required>
                                    <option value="0">TIDAK</option>
                                    <option value="1">YA</option>
                                </select>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary" name="submit" value="submit">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <h4 class="text-bold">Petunjuk:</h4>
                        <p>Lengkapilah form yang diperlukan di sebelah untuk dapat membuat data baru.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>

</script>
@endsection

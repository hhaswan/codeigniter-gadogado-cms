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
                            <div class="form-group">
                                <label>Nama Role</label>
                                <input type="text" class="form-control" name="nama" placeholder="Masukkan Data" required>
                            </div>
                            <div class="form-group">
                                <label>Level Akses</label>
                                <select name="access" class="form-control selectpicker" required>
                                    <option value="0">NORMAL</option>
                                    <option value="1">AKSES SEMUA DATA</option>
                                </select>
                            </div>
                            <div class="form-group" id="access-form">
                                <label>Area Akses Diluar Divisi Ini</label>
                                {{ $area }}
                                <small class="text-muted">Kosongkan bila divisi ini hanya dapat mengakses data dari divisi ini saja.</small>
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
    $(document).ready(function(){
        var access_val = $('#access-sel option:selected').val();
        if(access_val == 1){
            $('#access-form').fadeOut('fast');
        }else{
            $('#access-form').fadeIn('fast');
        }
    });

    $(document).on('change', '#access-sel', function(){
        var access_val = $('#access-sel option:selected').val();
        if(access_val == 1){
            $('#access-form').fadeOut('fast');
        }else{
            $('#access-form').fadeIn('fast');
        }
    });
</script>
@endsection

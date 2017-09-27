@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-title pull-left btn-group">
                    <a href="{{ str_replace('/detail/'.$id, '', base_url(uri_string())) }}" class="btn btn-primary btn-sm">
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
                    <div class="col-lg-8">
                        <h4 class="text-bold">Daftar Access</h4>
                        <p class="text-justify">Apabila terdapat access yang belum tersdia di tabel di bawah ini, dapat Anda update menggunakan fitur update entri.</p>
                        {{ $body }}
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Nama Module</label>
                            <h4 class="text-primary text-bold" style="margin:0px;">
                                {{ strtoupper($q[0]->name) }}
                            </h4>
                        </div>
                        <div class="form-group">
                            <label>Versi &amp; Tanggal Install Module</label>
                            <h5 class="text-primary text-bold" style="margin:0px;">
                                {{ "v{$q[0]->version}" }} | {{ \Carbon\Carbon::parse($q[0]->created_at)->diffForHumans(); }}
                            </h5>
                        </div>
                        <div class="form-group">
                            <label>Tipe Module</label>
                            <h5 class="text-primary text-bold" style="margin:0px;">
                                {{ ($q[0]->type == 'S') ? 'System' : 'Application' }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script></script>
@endsection

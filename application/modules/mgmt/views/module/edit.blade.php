@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-title pull-left btn-group">
                    <a href="{{ str_replace('/edit/'.$id, '', base_url(uri_string())) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> <span class="hidden-xs"> Management Data</span>
                    </a>
                </div>
                <div class="box-title pull-right btn-group">
                    @if($priv->add)
                    <a href="{{ str_replace('/edit/'.$id, '/create', base_url(uri_string())) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> <span class="hidden-xs">Tambah Data</span>
                    </a>
                    @endif
                </div>
            </div>
                <div class="box-body">
                    <div class="col-lg-7">
                        @if(flash('MSG_ERROR'))
                            <div class="text-danger text-center">{{flash('MSG_ERROR')}}</div>
                        @endif
                        <form method="post" action="{{ base_url(uri_string()) }}" enctype="multipart/form-data">
                            <input type="hidden" value="_patch" name="method" />
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
                            <div class="form-group">
                                <label>Upload File Update Module</label>
                                <input type="file" name="new_module" class="form-control" accept=".zip,application/x-zip,application/zip,application/x-zip-compressed,application/s-compressed,multipart/x-zip" required/>
                                <p class="help-block">Maksimal: <b>{{ ini_get('upload_max_filesize') }}</b>, Format: <b>ZIP</b></p>
                            </div>
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary" name="submit" value="submit">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <h4 class="text-bold">Petunjuk:</h4>
                        <p>Edit beberapa informasi di samping untuk melakukan perubahan terhadap data ini.</p>
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

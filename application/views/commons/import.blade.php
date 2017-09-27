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
                        <form method="post" action="{{ base_url(uri_string()) }}" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Format Import</label>
                                <h5 class="text-primary text-bold" style="margin:0px;">
                                    <a href="{{ base_url(uri_string().'/format') }}"><i class="fa fa-file-excel-o"></i> DOWNLOAD FORMAT</a>
                                </h5>
                            </div>
                            <div class="form-group">
                                <label>Upload File Untuk Diimport</label>
                                <input type="file" name="import_data" class="form-control" accept=".xls,application/vnd.ms-excel,application/msexcel,application/x-msexcel,application/x-ms-excel,application/x-excel,application/x-dos_ms_excel,application/xls,application/x-xls,application/excel,application/download,application/vnd.ms-office,application/msword" required/>
                                <p class="help-block">Maksimal: <b>{{ ini_get('upload_max_filesize') }}</b>, Format: <b>XLS</b></p>
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
                        <p class="text-justify">Untuk meng-import data, silakan download format excel yang tersedia kemudian masukkan data yang ingin di-import 
                        sesuai dengan format yang telah ditentukan.</p>
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
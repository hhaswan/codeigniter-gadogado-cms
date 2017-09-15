@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                @if(isset($filter))                
                <div class="box-title">
                    <button type="button" class="btn btn-primary btn-sm btn-filter" data-filter="default">
                        <i class="fa fa-search"></i> <span class="hidden-xs">Filter Data</span>
                    </button>
                </div>
                @endif
                <div class="box-title pull-right btn-group">
                    <button type="button" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> <span class="hidden-xs">Tambah Data</span>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> <span class="hidden-xs">Hapus Data</span>
                    </button>
                </div>
            </div>
                @if(isset($filter))
                <div class="box-body" id="default" style="box-shadow: inset 0px -11px 8px -10px #CCC;">
                    <div class="col-lg-6 col-lg-offset-3">
                        @include('commons.filter')
                    </div>
                </div>                
                @endif
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                    Explorer 4.0
                                </td>
                                <td>Win 95+</td>
                                <td> 4</td>
                                <td>X</td>
                            </tr>
                        </tbody>
                    </table>                        
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_css')
<link rel="stylesheet" href="{{base_url()}}adminlte/plugins/bootstrap-select/bootstrap-select.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@endsection

@section('custom_js')
<script src="{{base_url()}}adminlte/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{base_url()}}adminlte/components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{base_url()}}adminlte/components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
@endsection

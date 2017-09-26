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
                    @if($priv->add)
                    <a href="{{ base_url(uri_string().'/create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> <span class="hidden-xs">Tambah Data</span>
                    </a>
                    @endif
                    @if($priv->delete)
                    <button type="button" class="btn btn-danger btn-sm btn-erase" data-url="{{base_url(uri_string().'/delete')}}" disabled>
                        <i class="fa fa-trash"></i> <span class="hidden-xs">Hapus Data</span>
                    </button>
                    @endif                    
                </div>
            </div>
                @if(isset($filter))
                <div class="box-body" id="default" style="box-shadow: inset 0px -11px 8px -10px #CCC;">
                    <div class="col-lg-6 col-lg-offset-3">
                        @include('commons.filter')
                    </div>
                </div>                
                @endif
                <div class="box-body" id="table-result-box">
                    {{ $body }}
                </div>
                <div class="overlay" style="display:none">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script></script>
@endsection

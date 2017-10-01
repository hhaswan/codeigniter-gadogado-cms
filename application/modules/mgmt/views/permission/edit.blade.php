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
                    @if(isset($filter))
                    <button type="button" class="btn btn-primary btn-sm btn-filter" data-filter="default">
                        <i class="fa fa-search"></i> <span class="hidden-xs">Filter Data</span>
                    </button>
                    @endif
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
                    @if(isset($filter))
                    <div class="box-body" id="default" style="box-shadow: inset 0px -11px 8px -10px #CCC;">
                        <div class="col-lg-6 col-lg-offset-3">
                            @include('commons.filter')
                        </div>
                    </div>                
                    @endif
                    <div class="col-lg-8">
                        @if(flash('MSG_ERROR'))
                            <div class="text-danger text-center">{{flash('MSG_ERROR')}}</div>
                        @endif
                        <form method="post" action="{{ base_url(uri_string()) }}">
                            <input type="hidden" value="_patch" name="method" />
                            <div class="form-group">
                                <h4 class="text-bold">Daftar Permission: <span class="text-primary">{{ $q[0]->name }}</span></h4>
                                {{ $body }}
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary" name="submit" value="submit">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4">
                        <h4 class="text-bold">Petunjuk:</h4>
                        <p class="text-justify">Silakan centang pada pilihan yang ingin diijinkan beserta privilege-nya (apabila ada) untuk role ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
$('.check-action').on('ifChecked', function(event){
    var id      = $(this).data('id');
    var child   = $('.action-child[data-id="'+ id +'"]');

    child.each(function(){
        $(this).removeAttr('disabled','disabled');
        $(this).iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });
    });
    $('.child_'+id).removeAttr("style");
});
$('.check-action').on('ifUnchecked', function(event){
    var id      = $(this).data('id');
    var child   = $('.action-child[data-id="'+ id +'"]');

    child.each(function(){
        $(this).attr('disabled','disabled');
        $(this).iCheck('uncheck');
    });
    $('.child_'+id).css({
        background  : "#EEE"
    });
});
</script>
@endsection

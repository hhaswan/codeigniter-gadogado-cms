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
<script>
$('.check-all').on('ifChanged', function(event){
    if(event.target.checked){
        $('.check-all-child').each(function(){
            $(this).iCheck('check');
        });
    }else{
        var a = $('.check-all-child').filter(':checked').length;
        var b = $('.check-all-child').length;

        // bila semua child check, maka uncheck semua
        if(a == b){
            $('.check-all-child').each(function(){
                $(this).iCheck('uncheck');
                $('.btn-erase').attr('disabled','disabled');
            });
        }
    }
});

$('.check-all-child').on('ifChecked', function(event){
    $('.btn-erase').removeAttr('disabled','disabled');
});

$('.check-all-child').on('ifUnchecked', function(event){
    $('.check-all').iCheck('uncheck');
    var a = $('.check-all-child').filter(':checked').length;

    // bila semua child tidak check, maka disable button
    if(a == 0){
        $('.btn-erase').attr('disabled','disabled');
    }
});

$(document).on('click','.btn-erase-single',function(e){
    e.preventDefault();
    
    var d_id  = $(this).data('id');
    var d_url = $(this).data('url');

    if(d_id != '' && d_url != ''){
        swal({
            title: "Konfirmasi Aksi",
            html: "Apakah Anda yakin ingin menghapus entri ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
            cancelButtonText: 'BATAL'
        }).then(function () {
            $.ajax({
                url     : d_url,
                type    : "delete",
                data    : { id: d_id },
                dataType: "json",
                beforeSend: function(){
                    $('.overlay').fadeIn('fast');
                },
                success : function(data){
                    $('.overlay').fadeOut('fast');
                    $('#table-result-box').html(data.html);
                    if(data.status){
                        swal({
                            title: 'Aksi Berhasil',
                            html: 'Entri berhasil dihapus!',
                            type: 'success',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }else{
                        swal({
                            title: 'Aksi Gagal',
                            html: 'Entri gagal dihapus',
                            type: 'error',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }
                }
            });
        });
    }

});

$(document).on('click','.btn-erase',function(e){
    e.preventDefault();
    
    var d_url = $(this).data('url');
    var collection  = [];

    // hapus semua yg ada
    $('.check-all-child').filter(':checked').each(function(){
        collection.push($(this).data('id'));
    });

    // fire ajax
    if(collection.length != 0 && d_url != ''){
         swal({
            title: "Konfirmasi Aksi",
            html: "Apakah Anda yakin ingin menghapus entri ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
            cancelButtonText: 'BATAL'
        }).then(function () {
            $.ajax({
                url     : d_url,
                type    : "delete",
                data    : { id: collection },
                dataType: "json",
                beforeSend: function(){
                    $('.overlay').fadeIn('fast');
                },
                success : function(data){
                    $('.overlay').fadeOut('fast');
                    $('#table-result-box').html(data.html);
                    if(data.status){
                        swal({
                            title: 'Aksi Berhasil',
                            html: 'Entri berhasil dihapus!',
                            type: 'success',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }else{
                        swal({
                            title: 'Aksi Gagal',
                            html: 'Entri gagal dihapus',
                            type: 'error',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }
                }
            });
        });
    }
});
</script>
@endsection

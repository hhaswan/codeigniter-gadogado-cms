@extends('layouts.management.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-title pull-left btn-group">
                    <a href="{{ back() }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> <span class="hidden-xs"> Import Data</span>
                    </a>
                </div>
            </div>
                <div class="box-body">
                    <div class="col-lg-8">
                        <form method="post" action="{{ base_url(uri_string()) }}">
                            <div class="form-group">
                                <h4 class="text-bold">Preview Import</h4>
                                {{ $body }}
                            </div>
                            @if($error < $rows)
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary" id="btn-submit" name="submit" value="submit">
                                    Lanjutkan &amp; Simpan
                                </button>
                            </div>
                            @endif
                        </form>
                    </div>
                    <div class="col-lg-4">
                        <h4 class="text-bold">Petunjuk:</h4>
                        <p class="text-justify">
                            Silakan cek data di bawah ini sebelum diimport ke dalam sistem, apabila ada kesalahan, Anda dapat mengupload ulang
                            dengan data import yang baru.
                        </p>
                        @if($error > 0)
                        <h4 class="text-bold text-red">Perhatian:</h4>
                        <p class="text-justify">
                            Terdapat <b class="text-red">{{ $error }}</b> data error dan perlu diperbaiki terlebih dahulu. Apabila Anda tetap
                            ingin melanjutkan untuk mengimport data ini, maka data error tersebut tidak akan diproses.
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
var is_submit = false;
$('#btn-submit').click(function(){
    is_submit = true;
});
$(window).bind('beforeunload', function(e) {
    if(! is_submit){
        return "Do you really want to close?";
    }
});
$(window).on('unload', function() {
    if(! is_submit){
        $.ajax({
            url     : "{{ str_replace("import_preview/".$id, "delete_import", base_url(uri_string())) }}",
            type    : "delete",
            data    : { id: "{{ decrypt($id) }}" },
            async   : false
        }); 
    }
});
</script>
@endsection
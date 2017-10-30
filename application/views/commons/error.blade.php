@extends('layouts.management.master')
@section('content')
<div class="error-page" style="margin-top:14%">
    <h2 class="headline text-yellow" style="margin-top:1%">{{ $error_code }}</h2>

    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> Oops! <b>{{ $title }}</b></h3>
        
        <p class="text-justify">
            @if(empty($error_msg))
            Maaf, kami tidak dapat menemukan halaman yang Anda minta. Hal ini bisa saja dikarenakan 
            oleh data ini telah dihapus, dimodiikasi maupun telah habis masa berlakunya.
            @else
            {{ $error_msg }}
            @endif
        </p>
        <div class="form-group">
            <a href="{{ back() }}" class="btn btn-primary"><i class="fa fa-arrow-left fa-fw"></i> <span class="hidden-xs">Kembali Ke Halaman Sebelumnya</span></a>
            <a href="{{ base_url('/mgmt') }}" class="btn btn-warning"><i class="fa fa-home fa-fw"></i> <span class="hidden-xs">Beranda</span></a>
        </div>
    </div>
</div>
@endsection
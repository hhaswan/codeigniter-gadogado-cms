@extends('layouts.login.master')
@section('content')
<div class="col-lg-8 col-lg-offset-2" style="margin-top:7%; ">
	
	<div class="login-box-body">
		<div class="login-logo">
			<a href="{{base_url()}}"><b>Code</b>Igniter</a>
		</div>
		<h4 class="text-center text-bold text-uppercase">AKUN BERHASIL DIBUAT</h4>
        <p class="text-center">
            Selamat! Untuk dapat menggunakan layanan, Anda harus mengkonfirmasi akun Anda melalui Email Anda terlebih dahulu.<br/>
            Bila menemui kesulitan, silakan menghubungi Bantuan atau Administrator.
            <br/><br/>
            Terima Kasih
        </p>
        <span class="col-lg-12 text-center">
            <a href="{{base_url()}}">HALAMAN UTAMA</a> | 
            <a href="@php if(! app()->secure_login) echo base_url('login'); else echo base_url('login/'.app()->login_identifier) @endphp">LOGIN</a>
        </span>
        <br/>
	</div>
    <div class="login-box-body text-center" style="background:#EEE">
        &COPY; 2017 - {{app()->name}}
    </div>
</div>
@endsection

@section('custom_js')
<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
    
    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red'
    })

  });
</script>
@endsection
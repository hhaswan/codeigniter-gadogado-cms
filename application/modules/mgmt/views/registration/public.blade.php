@extends('layouts.login.master')
@section('content')
<div class="login-box" style="margin-top:30px;">
	
	<div class="login-box-body">
		<div class="login-logo">
			<a href="{{base_url()}}"><b>Code</b>Igniter</a>
		</div>
		<h4 class="text-center">Pendaftaran Akun Baru</h4>
        @if(flash('MSG_ERROR'))
		<div class="text-danger text-center">{{flash('MSG_ERROR')}}</div>
        @endif
		<br/>
		<form method="POST" action="{{base_url(uri_string())}}">
            <div class="form-group has-feedback">
                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
				<span class="fa fa-user form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
				<span class="fa fa-envelope form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
                <label for="password">Password <span class="text-danger">*</span></label>            
				<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
				<span class="fa fa-lock form-control-feedback"></span>
			</div>
            <div class="form-group has-feedback">
                <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
				<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
				<span class="fa fa-lock form-control-feedback"></span>
			</div>
            <div class="form-group"> 
                <input type="checkbox" id="tos" class="flat-red" name="tos" value="1" required> <label for="tos">Saya mensetujui <a href="#" class="text-bold" data-toggle="tooltip" title="Baca Syarat dan Ketentuan">S & K</a></label>
			</div>
            <br/>
            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" class="btn col-lg-12 btn-danger" name="submit" value="submit">Daftar</button>
                </div>
			</div>
		</form>
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
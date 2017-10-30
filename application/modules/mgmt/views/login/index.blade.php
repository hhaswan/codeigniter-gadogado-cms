@extends('layouts.login.master')
@section('content')
<div class="login-box">
	
	<div class="login-box-body">
		<div class="login-logo">
			<a href="{{base_url()}}"><b>{{ app()->name }}</b></a>
		</div>
		<h4 class="text-center">Masuk Ke Akun Anda</h4>
        @if(flash('MSG_ERROR'))
		<div class="text-danger text-center">{{flash('MSG_ERROR')}}</div>
        @endif
		<br/>
		<form method="POST" action="{{base_url(uri_string())}}">
			<div class="form-group has-feedback">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
				<span class="fa fa-envelope form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
                <label for="password">Password</label>            
				<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
				<span class="fa fa-lock form-control-feedback"></span>
			</div>
            <div class="row">
                <div class="col-lg-6">
                    @if(app()->public_register)
                    <a href="{{base_url('/register')}}"><i class="fa fa-user"></i> Daftarkan Akun</a>
                    @endif
                </div>
                <div class="col-lg-6 text-right">
                    <a href="@php if(! app()->secure_login) echo base_url('forgot'); else echo base_url('forgot/'.app()->login_identifier) @endphp"><i class="fa fa-lock"></i> Lupa Password</a>
                </div>
			</div>
            <br/>
            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" class="btn col-lg-12 btn-danger" name="submit" value="submit">Masuk</button>
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
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
@endsection
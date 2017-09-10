@extends('layouts.login.master')
@section('content')
<div class="login-box">
	
	<div class="login-box-body">
		<div class="login-logo">
			<a href="{{base_url()}}"><b>Code</b>Igniter</a>
		</div>
		<h4 class="text-center">Lupa Password</h4>
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
            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" class="btn col-lg-12 btn-danger" name="submit_send" value="submit_send">Kirim</button>
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
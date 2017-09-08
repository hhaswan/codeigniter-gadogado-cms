<!DOCTYPE html>
<html>
<head>
	@include('layouts.management.header')
</head>
<body class="hold-transition wysihtml5-supported skin-red fixed sidebar-mini">
	<div class="wrapper">
		@include('layouts.management.nav')
		@include('layouts.management.sidebar')
		<div class="content-wrapper">
			<section class="content-header">
				<h1>{{$title}}</h1>
				<ol class="breadcrumb">
					@foreach(breadcrumb('mgmt') as $key => $row)
						@if($key != 'li-active')
							<li><a href="{{$key}}">{{$row}}</a></li>
						@else
							<li class="active">{{$row}}</li>
						@endif
					@endforeach
				</ol>
			</section>
			<section class="content">
				@yield('content')
			</section>
		</div>
		<footer class="main-footer">
			<div class="pull-right hidden-xs"><b>Framework Version</b> {{CI_VERSION}}</div>
			<strong>Copyright &copy; {{Carbon\Carbon::now()->format('Y')}} - <a href="{{base_url()}}">{{app()->name}}</a></strong>
		</footer>
	</div>
	@include('layouts.management.footer')
</body>
</html>
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
			@if(! isset($error_page))
			<section class="content-header">
				<h1>{{$title}}</h1>
				<ol class="breadcrumb">
					@foreach(breadcrumb('mgmt') as $key => $row)
						@if($key != 'li-active')
							<li><a href="{{$key}}">{{ $row }}</a></li>
						@else
							<li class="active">
								@if(strlen($row) > 30) 
									{{ substr($row, 0, 30) }}
								@else 
									{{ $row }}
								@endif
							</li>
						@endif
					@endforeach
				</ol>
			</section>
			@endif
			<section class="content">
				@yield('content')
			</section>
		</div>
		<footer class="main-footer">
			<div class="pull-right hidden-xs"><b>Versi Aplikasi</b> {{app()->version}}</div>
			<strong>Hak Cipta &copy; {{Carbon\Carbon::now()->format('Y')}} - <a href="{{base_url()}}">{{app()->name}}</a></strong>
		</footer>
	</div>
	@include('layouts.management.footer')
</body>
</html>
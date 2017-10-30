<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@php if(isset($title)) echo $title. ' - ' .app()->tagline; else echo app()->tagline; @endphp</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<!-- Caching Control -->
<meta http-equiv="Cache-control" content="private">
<meta http-equiv="Cache-control" content="max-age=60">
<meta name="theme-color" content="{{app()->color_front}}" />

<link rel="apple-touch-icon" href="{{app()->template_back}}apple-touch-icon.png">
<link rel="icon" href="{{app()->template_back}}favicon.ico">

<link rel="stylesheet" href="{{app()->template_back}}components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="{{app()->template_back}}components/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="{{app()->template_back}}components/Ionicons/css/ionicons.min.css">
<link rel="stylesheet" href="{{app()->template_back}}css/AdminLTE.min.css">
<link rel="stylesheet" href="{{app()->template_back}}plugins/iCheck/all.css">
<link rel="stylesheet" href="{{app()->template_back}}plugins/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
@yield('custom_css')
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
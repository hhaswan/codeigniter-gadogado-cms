<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@php if(isset($title)) echo $title. ' - ' .app()->tagline; else echo app()->tagline; @endphp
</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<!-- Twitter -->
<meta name="twitter:site" content="@getbootstrap">
<meta name="twitter:creator" content="@getbootstrap">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Bootstrap">
<meta name="twitter:description" content="The most popular HTML, CSS, and JS library in the world.">
<meta name="twitter:image" content="http://getbootstrap.com/assets/brand/bootstrap-social.png">

<!-- Facebook -->
<meta property="og:url" content="http://getbootstrap.com">
<meta property="og:title" content="Bootstrap">
<meta property="og:description" content="The most popular HTML, CSS, and JS library in the world.">
<meta property="og:image" content="http://getbootstrap.com/assets/brand/bootstrap-social.png">
<meta property="og:image:secure_url" content="http://getbootstrap.com/assets/brand/bootstrap-social.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<meta name="description" content="The most popular HTML, CSS, and JS library in the world.">
<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">

<link rel="apple-touch-icon" href="{{base_url()}}adminlte/apple-touch-icon.png">
<link rel="icon" href="{{base_url()}}adminlte/favicon.ico">

<link rel="stylesheet" href="{{base_url()}}adminlte/components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/components/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/components/Ionicons/css/ionicons.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/css/AdminLTE.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/css/AdminLTERed.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/components/bootstrap-daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/plugins/iCheck/all.css">
<link rel="stylesheet" href="{{base_url()}}adminlte/plugins/sweetalert2/sweetalert2.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
@yield('custom_css')
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
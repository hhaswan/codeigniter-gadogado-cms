<script src="{{app()->template_back}}components/jquery/dist/jquery.min.js"></script>
<script src="{{app()->template_back}}components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{app()->template_back}}plugins/iCheck/icheck.min.js"></script>
<script src="{{app()->template_back}}plugins/sweetalert2/sweetalert2.min.js"></script>
@yield('custom_js')
@if(flash('GLOBAL_ALERT_SUCCESS'))
<script>
    swal({
        title: 'Aksi Berhasil',
        html: '{{flash('GLOBAL_ALERT_SUCCESS')}}',
        type: 'success',
        timer: 2500
    }).then(
        function () {},
        function (dismiss) {}
    );
</script>
@elseif(flash('GLOBAL_ALERT_FAIL'))
<script>
    swal({
        title: 'Aksi Gagal',
        html: '{{flash('GLOBAL_ALERT_FAIL')}}',
        type: 'error',
        timer: 2500
    }).then(
        function (dismiss) {}
    );
</script>
@endif
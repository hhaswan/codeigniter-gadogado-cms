<script src="{{base_url()}}adminlte/components/jquery/dist/jquery.min.js"></script>
<script src="{{base_url()}}adminlte/components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/iCheck/icheck.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
@yield('custom_js')
@if(flash('GLOBAL_ALERT_SUCCESS'))
<script>
    swal({
        title: 'Aksi Berhasil',
        text: '{{flash('GLOBAL_ALERT_SUCCESS')}}',
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
        text: '{{flash('GLOBAL_ALERT_FAIL')}}',
        type: 'error',
        timer: 2500
    }).then(
        function (dismiss) {}
    );
</script>
@endif
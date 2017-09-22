<script src="{{base_url()}}adminlte/components/jquery/dist/jquery.min.js"></script>
<script src="{{base_url()}}adminlte/components/jquery-ui/jquery-ui.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/iCheck/icheck.min.js"></script>
<script src="{{base_url()}}adminlte/components/moment/min/moment.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{base_url()}}adminlte/components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{base_url()}}adminlte/components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="{{base_url()}}adminlte/components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{base_url()}}adminlte/components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="{{base_url()}}adminlte/components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="{{base_url()}}adminlte/components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="{{base_url()}}adminlte/js/adminlte.min.js"></script>
@yield('custom_js')
<script src="{{base_url()}}adminlte/js/app.js"></script>
@if(flash('GLOBAL_ALERT_SUCCESS'))
<script>
    swal({
        title: 'Aksi Berhasil',
        html: '{{flash('GLOBAL_ALERT_SUCCESS')}}',
        type: 'success',
        confirmButtonClass: 'btn btn-primary',
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
        confirmButtonClass: 'btn btn-primary',
        timer: 2500
    }).then(
        function (dismiss) {}
    );
</script>
@endif
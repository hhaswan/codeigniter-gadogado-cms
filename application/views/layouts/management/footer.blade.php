<script src="{{app()->template_back}}components/jquery/dist/jquery.min.js"></script>
<script src="{{app()->template_back}}components/jquery-ui/jquery-ui.min.js"></script>
<script src="{{app()->template_back}}plugins/iCheck/icheck.min.js"></script>
<script src="{{app()->template_back}}components/moment/min/moment.min.js"></script>
<script src="{{app()->template_back}}plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{app()->template_back}}components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{app()->template_back}}plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="{{app()->template_back}}components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="{{app()->template_back}}components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{app()->template_back}}components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="{{app()->template_back}}components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="{{app()->template_back}}components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="{{app()->template_back}}js/adminlte.min.js"></script>
@yield('custom_js')
<script src="{{app()->template_back}}js/app.js"></script>
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
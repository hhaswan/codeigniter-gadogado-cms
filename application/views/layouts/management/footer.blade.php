<script src="{{base_url()}}adminlte/components/jquery/dist/jquery.min.js"></script>
<script src="{{base_url()}}adminlte/components/jquery-ui/jquery-ui.min.js"></script>
<script>$.widget.bridge('uibutton', $.ui.button);</script>
<script src="{{base_url()}}adminlte/components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{base_url()}}adminlte/components/raphael/raphael.min.js"></script>
<script src="{{base_url()}}adminlte/components/morris.js/morris.min.js"></script>
<script src="{{base_url()}}adminlte/components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="{{base_url()}}adminlte/components/jquery-knob/dist/jquery.knob.min.js"></script>
<script src="{{base_url()}}adminlte/components/moment/min/moment.min.js"></script>
<script src="{{base_url()}}adminlte/components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="{{base_url()}}adminlte/components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="{{base_url()}}adminlte/components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="{{base_url()}}adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{base_url()}}adminlte/components/fastclick/lib/fastclick.js"></script>
<script src="{{base_url()}}adminlte/js/adminlte.min.js"></script>
<script src="{{base_url()}}adminlte/js/pages/dashboard.js"></script>
<script src="{{base_url()}}adminlte/js/demo.js"></script>
@yield('custom_js')
<script>
swal({
  title: 'Error!',
  text: 'Do you want to continue',
  type: 'error',
  confirmButtonText: 'Cool'
});
</script>
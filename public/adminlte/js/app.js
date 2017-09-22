// iCheckbox
$('input[type="checkbox"].icheck, input[type="radio"].icheck').iCheck({
    checkboxClass: 'icheckbox_flat-blue',
    radioClass: 'iradio_flat-blue'
});

// filter data box
$('.btn-filter').click(function(){
    var filter_for = $(this).data('filter');
    var result_box = $('#'+filter_for);
    result_box.slideToggle('medium');
});

// datatable
$('.datatable').DataTable({
    "order": [],
    "columnDefs": [ {
        "targets"   : 'no-sort',
        "orderable" : false,
        "className" : "text-center"
    } ],
    "language": {
        "search"        : "Cari Entri:",
        "info"          : "Entri ke _START_ - _END_ dari _TOTAL_",
        "infoEmpty"     : "Entri ke 0",
        "infoFiltered"  : "( _MAX_ entri terfilter)",
        "lengthMenu"    : "Tampilkan _MENU_",
        "zeroRecords"   : "Entri Tidak Ditemukan",
        "paginate"      : {
                            "first"     : "Pertama",
                            "last"      : "Terakhir",
                            "next"      : "Berikutnya",
                            "previous"  : "Sebelumnya"
                          }
    }
});

// Bootstrap Tooltip
$('[data-toggle="tooltip"]').tooltip();

// sweetalert confirm
$(document).on("click", ".confirm-action", function(e) { 
    e.preventDefault();
    var link = $(this).attr('href');
    swal({
        title: "Konfirmasi Aksi",
        html: "Apakah Anda yakin dengan aksi ini?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK',
        confirmButtonClass: 'btn btn-primary',
        cancelButtonClass: 'btn btn-danger',
        cancelButtonText: 'BATAL'
      }).then(function () {
        if(link != ''){
            $(location).attr('href', link);
        }
    });
});
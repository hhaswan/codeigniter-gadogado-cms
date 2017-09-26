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
function reinitialize_datatable(){
    $('input[type="checkbox"].icheck, input[type="radio"].icheck').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });

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
}

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

// Per Module Basis
$('.check-all').on('ifChanged', function(event){
    if(event.target.checked){
        $('.check-all-child').each(function(){
            $(this).iCheck('check');
        });
    }else{
        var a = $('.check-all-child').filter(':checked').length;
        var b = $('.check-all-child').length;

        // bila semua child check, maka uncheck semua
        if(a == b){
            $('.check-all-child').each(function(){
                $(this).iCheck('uncheck');
                $('.btn-erase').attr('disabled','disabled');
            });
        }
    }
});

$('.check-all-child').on('ifChecked', function(event){
    $('.btn-erase').removeAttr('disabled','disabled');
});

$('.check-all-child').on('ifUnchecked', function(event){
    $('.check-all').iCheck('uncheck');
    var a = $('.check-all-child').filter(':checked').length;

    // bila semua child tidak check, maka disable button
    if(a == 0){
        $('.btn-erase').attr('disabled','disabled');
    }
});

$(document).on('click','.btn-erase-single',function(e){
    e.preventDefault();
    
    var d_id  = $(this).data('id');
    var d_url = $(this).data('url');
    var d_rdr = $(this).data('redirect');

    if(d_id != '' && d_url != ''){
        swal({
            title: "Konfirmasi Aksi",
            html: "Apakah Anda yakin ingin menghapus entri ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
            cancelButtonText: 'BATAL'
        }).then(function () {
            $.ajax({
                url     : d_url,
                type    : "delete",
                data    : { id: d_id },
                dataType: "json",
                beforeSend: function(){
                    if(d_rdr == '' && d_rdr === undefined){
                        // normal table
                        $('.overlay').fadeIn('fast');
                    }
                },
                success : function(data){
                    $('.overlay').fadeOut('fast');
                    
                    if(data.status){
                        swal({
                            title: 'Aksi Berhasil',
                            html: 'Entri berhasil dihapus!',
                            type: 'success',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });

                        if(d_rdr == '' && d_rdr === undefined){
                            $('#table-result-box').html(data.html);
                            reinitialize_datatable();
                        }else{
                            // redirect
                            $(location).attr('href', d_rdr);                            
                        }
                    }else{
                        swal({
                            title: 'Aksi Gagal',
                            html: 'Entri gagal dihapus',
                            type: 'error',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }
                }
            });
        });
    }

});

$(document).on('click','.btn-erase',function(e){
    e.preventDefault();
    
    var d_url = $(this).data('url');
    var collection  = [];

    // hapus semua yg ada
    $('.check-all-child').filter(':checked').each(function(){
        collection.push($(this).data('id'));
    });

    // fire ajax
    if(collection.length != 0 && d_url != ''){
         swal({
            title: "Konfirmasi Aksi",
            html: "Apakah Anda yakin ingin menghapus entri ini?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
            cancelButtonText: 'BATAL'
        }).then(function () {
            $.ajax({
                url     : d_url,
                type    : "delete",
                data    : { id: collection },
                dataType: "json",
                beforeSend: function(){
                    $('.overlay').fadeIn('fast');
                },
                success : function(data){
                    $('.overlay').fadeOut('fast');
                    $('#table-result-box').html(data.html);
                    if(data.status){
                        reinitialize_datatable();
                        swal({
                            title: 'Aksi Berhasil',
                            html: 'Entri berhasil dihapus!',
                            type: 'success',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }else{
                        swal({
                            title: 'Aksi Gagal',
                            html: 'Entri gagal dihapus',
                            type: 'error',
                            confirmButtonClass: 'btn btn-primary',
                            timer: 2500
                        });
                    }
                }
            });
        });
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
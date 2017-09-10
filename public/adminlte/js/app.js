$(document).on("click", ".confirm-action", function(e) { 
    e.preventDefault();
    var link = $(this).attr('href');
    swal({
        title: "Konfirmasi Aksi",
        text: "Apakah Anda yakin dengan aksi ini?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK',
        cancelButtonText: 'BATAL'
      }).then(function () {
        if(link != ''){
            $(location).attr('href', link);
        }
    });
});
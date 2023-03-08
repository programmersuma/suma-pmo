$(document).ready(function () {
    $('body').on('click', '#btnDetailEkspedisi', function (e) {
        e.preventDefault();

        var images = '<div class="mx-auto mb-2 d-flex w-200px h-200px bgi-no-repeat bgi-size-contain bgi-position-center"'+
            'style="background-image:url('+$(this).data("images")+')">'+
        '</div>';

        $('#modalImages').html(images);
        $('#modalID').text($(this).data("id"));
        $('#modalKode').text($(this).data("kode"));
        $('#modalNama').text($(this).data("nama"));

        $('#modalDetailEkspedisi').modal('show');
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $('body').on('click', '#btnSimpanEkspedisi', function (e) {
        e.preventDefault();

        var id = $(this).data("id");
        var kode = $(this).data("kode");
        var nama = $(this).data("nama");
        var _token = $('input[name="_token"]').val();

        if(id == '' || kode == '' || nama == '') {
            Swal.fire({
                text: 'Isi data secara lengkap atau pilih data logistic terlebih dahulu',
                icon: 'warning',
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Ok, got it!',
                customClass: {
                    confirmButton: 'btn btn-warning'
                }
            });
        } else {
            loading.block();
            $.ajax({
                url: url.simpan_ekspedisi,
                method: "post",
                data: {
                    id: id, kode: kode, nama: nama, token: _token
                },
                success: function(response) {
                    loading.release();
                    if (response.status == true) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success',
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: 'warning',
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                },
                error: function() {
                    loading.release();
                    Swal.fire({
                        text: 'Server tidak merespon, coba lagi',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        }
    });
});

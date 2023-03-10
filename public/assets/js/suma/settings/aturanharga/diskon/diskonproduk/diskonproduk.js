// pagination,search,per_page
const params = new URLSearchParams(window.location.search)
for (const param of params) {
    let request = JSON.parse(atob(params.get('param')));
    var search = request.search;
    var per_page = request.per_page;
    var page = request.page;
}

function editData(data) {
    // console.log(data);
    $('#produk').val(data.kode_produk.trim());
    $('#produk').attr('readonly', true);
    $('#produk').addClass('bg-secondary');
    $('#nama_produk').val(data.nama_produk);
    $('#cabang option[value="' + data.cabang.trim() + '"]').prop('selected', true);
    $('#cabang option:not(:selected)').attr('disabled', true);
    $('#cabang').addClass('bg-secondary');
    $('#umur_faktur').val(data.umur_faktur);
    $('#disc_normal').val(data.disc_normal == '.00' ? 0 : data.disc_normal);
    $('#disc_max').val(data.disc_max == '.00' ? 0 : data.disc_max);
    $('#disc_plus_normal').val(data.disc_plus_normal == '.00' ? 0 : data.disc_plus_normal);
    $('#disc_plus_max').val(data.disc_plus_max == '.00' ? 0 : data.disc_plus_max);
    $('#tambah_diskon_produk form .modal-body').find('input[required], select[required]').each(function () {
        if ($(this).val() == '') {
            $(this).addClass('is-invalid');
            if (!$(this).next().hasClass('invalid-feedback')) {
                $(this).after('<div class="invalid-feedback">Tidak boleh kosong</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next().remove();
        }
    });
    $('#tambah_diskon_produk').modal('show');
    // $('#tambah_diskon_produk > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'submit');
}
// end edit data

// merubah url dengan parameter yang baru + reload
function gantiUrl(page = current_page, data = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + "?param=" + btoa(JSON.stringify({page: page, per_page: $('#kt_project_users_table_length > label > select').val(), search: $('#filterSearch').val()}));
    // "?page=" + page + "&per_page=" + $('#kt_project_users_table_length > label > select').val() + "&search=" + $('#filterSearch').val();
}
// end pagination,search,per_page

$(document).ready(function () {
    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loading.block();
    });
    // end form
    // ajax start loading
    $(document).ajaxStart(function () {
        loading.block();
    });
    // ajax stop loading
    $(document).ajaxStop(function () {
        loading.release();
    });
    // end ajax

    $('#tambah_diskon_produk form .modal-body').find('input, select').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            var index = $('#tambah_diskon_produk form .modal-body').find('input, select').index(this) + 1;
            if ($('#tambah_diskon_produk form .modal-body').find('input, select').eq(index).attr('readonly') || $('#tambah_diskon_produk form .modal-body').find('input, select').eq(index).hasClass('bg-secondary')) {
                for (let i = index; i < $('#tambah_diskon_produk form .modal-body').find('input, select').length; i++) {
                    if (!$('#tambah_diskon_produk form .modal-body').find('input, select').eq(i).attr('readonly') || !$('#tambah_diskon_produk form .modal-body').find('input, select').eq(i).hasClass('bg-secondary')) {
                        $('#tambah_diskon_produk form .modal-body').find('input, select').eq(i).focus();
                        break;
                    }
                }
            } else {
                $('#tambah_diskon_produk form .modal-body').find('input, select').eq(index).focus();
            }
        }
    });


    if (old.cabang != null) {
        $('#cabang option[value="' + old.cabang.trim() + '"]').prop('selected', true);
        $('#tambah_diskon_produk > div > div > form > div.modal-footer > button.btn.btn-primary').attr('type', 'button');
    }


    // end responsive ukuran layar

    // search
    $('#filterSearch').val(search);
    $('#filterSearch').on('change keydown', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            gantiUrl(1);
        }
    });
    // end search
    

    // validasi inputan kode produk
    $('#produk').on('change', function () {
        $.ajax({
            url: base_url + '/validasi/produk',
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                kd_produk: this.value
            },
            success: function (data) {
                if (data.status == 1) {
                    $('#nama_produk').val(data.data.nama_produk);
                    $('#produk').removeClass('is-invalid');
                    $('#produk').addClass('is-valid');
                    $('#tambah_diskon_produk > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', 'kirim');
                } else if (data.status == 0) {
                    $('#nama_produk').val('');
                    $('#produk').removeClass('is-valid');
                    $('#produk').addClass('is-invalid');
                    $('#tambah_diskon_produk > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
                }
            },
            error: function (data) {
                $('#nama_produk').val('');
                $('#produk').removeClass('is-valid');
                $('#produk').addClass('is-invalid');
                $('#tambah_diskon_produk > div > div > form > div.modal-footer > button.btn.btn-primary').attr('id', '');
            }
        });
    });
    // end validasi inputan kode produk

    // validasi data produk
    $('#produk, #cabang').on('change', function () {
        if ($('#produk').val() != '' && $('#cabang').val() != '') {
            $.ajax({
                url: base_url + '/setting/diskonproduk/cekproduk',
                type: "POST",
                data: {
                    _token: $('input[name="_token"]').val(),
                    kd_produk: $('#produk').val(),
                    cabang: $('#cabang').val()
                },
                success: function (data) {
                    // console.log(data);
                    if (data.status == 1) {
                        loading.release();
                        Swal.fire({
                            title: 'Informasi',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Edit!',
                            cancelButtonText: 'Tidak !',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-danger'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#tambah_diskon_produkLabel').html('Edit Diskon Produk');
                                editData(data.data);
                            } else {
                                $('#produk').val('');
                                $('#nama_produk').val('');
                                $('#produk').removeClass('is-valid');
                                $('#produk').removeClass('is-invalid');
                                $('#cabang option[value=""]').prop('selected', true);
                                $('#tambah_diskon_produk > div > div > form').trigger('reset');
                            }
                        });
                    } else if (data.status == 0) {
                        loading.release();
                    }
                },
                error: function (data) {
                }
            });
        }
    });
    // end validasi data produk


    // delete data
    $('.btn-delete').on('click', function () {
        $('#delet_model #produk').val($(this).data('p').trim());
        $('#delet_model #cabang').val($(this).data('c').trim());

        $('#form > div.modal-body > div > p.ms-text').html('Apakah anda yakin ingin menghapus diskon produk<br> Produk : <b>' + $(this).data('p') + '</b>, pada cabang : <b>' + $(this).data('c') + '</b> ?');
    });
    // end delete data

    // edit data
    $('.btn-edit').on('click', function () {
        $('#tambah_diskon_produkLabel').html('Edit Diskon Produk');
        var data = $(this).data('array');
        editData(data);
    });

    

    //  add data hanya menganti label di modal dan mengosongkan inputan
    $('#btn-adddiskonproduk').on('click', function () {
        $('#tambah_diskon_produkLabel').html('Tambah Diskon Produk');
        $('#tambah_diskon_produk > div > div > form').trigger('reset');
        $('#produk').removeAttr('readonly');
        $('#produk').removeClass('bg-secondary');
        $('#produk').removeClass('is-valid');
        $('#produk').removeClass('is-invalid');
        $('#cabang option').removeAttr('disabled');
        $('#cabang').removeClass('bg-secondary');
        $('#cabang option[value=""]').prop('selected', true);
    });
    // end add data

    $('#tambah_diskon_produk form .modal-footer').find('#kirim').on('click', function (e) {
        $('#tambah_diskon_produk form .modal-body').find('input[required], select[required]').each(function () {
            if ($(this).val() == '') {
                $(this).addClass('is-invalid');
                if (!$(this).next().hasClass('invalid-feedback')) {
                    $(this).after('<div class="invalid-feedback">Tidak boleh kosong</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next().remove();
            }
        });

        // swal fire confirm apakah yakin akan mengirim data ambil inputan kode dealer pada carbang jika iya triger submit pada #tambah_diskon_produk form
        if (!$('#tambah_diskon_produk form .modal-body').find('input[required], select[required]').hasClass('is-invalid')) {
            swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah yakin akan mengirim data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $('#tambah_diskon_produk form').submit();
                }
            });
        }
    });
});

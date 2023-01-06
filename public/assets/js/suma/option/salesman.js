function loadDataOptionSalesman(page = 1, per_page = 10, search = '') {
    loading.block();
    $.ajax({
        url: base_url + '/option/salesman' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function(response) {
            loading.release();

            if (response.status == false) {
                Swal.fire({
                    text: response.message,
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
            } else {
                $('#optionSalesmanContentModal').html(response.data);
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

$(document).ready(function () {
    $(document).on('click', '#formOptionSalesman #paginationOptionSalesman .page-item a', function () {
        var page = $(this)[0].getAttribute("data-page");
        var per_page = $('#formOptionSalesman #selectPerPageOptionSalesman').val();
        var search = $('#formOptionSalesman #inputSearchOptionSalesman').val();

        loadDataOptionSalesman(page, per_page, search);
    });

    $('body').on('change', '#formOptionSalesman #selectPerPageOptionSalesman', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionSalesman #selectPerPageOptionSalesman').val();
        var start_record = $('#formOptionSalesman #startRecordOptionSalesman').html();
        var page = Math.ceil(start_record / per_page);
        var search = $('#formOptionSalesman #inputSearchOptionSalesman').val();

        loadDataOptionSalesman(page, per_page, search);
    });

    $('body').on('click', '#formOptionSalesman #btnSearchOptionSalesman', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionSalesman #selectPerPageSalesman').val();
        var search = $('#formOptionSalesman #inputSearchOptionSalesman').val();

        loadDataOptionSalesman(1, per_page, search);
    });

    $('#formOptionSalesman #inputSearchOptionSalesman').on('change', function (e) {
        e.preventDefault();
        var per_page = $('#formOptionSalesman #selectPerPageSalesman').val();
        var search = $('#formOptionSalesman #inputSearchOptionSalesman').val();

        loadDataOptionSalesman(1, per_page, search);
    });
});

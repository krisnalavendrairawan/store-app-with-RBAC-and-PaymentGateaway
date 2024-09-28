function showSuccessMessage(message, redirectUrl) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = redirectUrl;
    });
}

function showErrorMessage(errors) {
    let errorMessage = '';
    $.each(errors, function(key, value) {
        errorMessage += value[0] + '\n';
    });
    Swal.fire({
        title: 'Error!',
        text: errorMessage,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

function deleteOption(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        response.success,
                        'success'
                    ).then(() => {
                        // Periksa apakah tabel masih ada dan valid
                        var table = $('#table-product').DataTable();
                        if ($.fn.DataTable.isDataTable('#table-product')) {
                            table.ajax.reload(null, false);
                        } else {
                            // Jika tabel tidak valid, reload halaman
                            window.location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    Swal.fire(
                        'Error!',
                        errorMessage,
                        'error'
                    );
                }
            });
        }
    });
}
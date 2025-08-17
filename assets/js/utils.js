function _handle_json_res(response) {
    if (response.success) {
        toastr.success("Thao tác thành công");
        setTimeout(function() {
            window.location.reload();
        }, 1500);

    } else {
        toastr.error(response.message ?? "Lỗi không xác định");
    }
}

function time(d)
{
    return moment.unix((d)).format("Y-MM-DD   HH:mm:ss");
}
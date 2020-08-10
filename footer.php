<script src="https://code.jquery.com/jquery-3.5.1.min.js" type="application/javascript"></script>

<script>
    function deleteMessage(id) {
        Swal.fire({
            title: '確定要刪除嗎？',
            text: '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '是',
            cancelButtonText: '否'
        }).then((result) => {
            if (result.value) {
                $('#FormDelete_' + id).submit();
            }
        })
    }

    function setLocation() {
        window.location = './';
    }

    $(document).ready(function () {
        if ($alert === 'success') {
            Swal.fire('Success', '', 'success').then(function () {
                setLocation();
            });
        }

        if ($alert === 'success_add') {
            Swal.fire('新增成功', '', 'success').then(function () {
                setLocation();
            });
        }

        if ($alert === 'success_delete') {
            Swal.fire('刪除成功', '', 'success').then(function () {
                setLocation();
            });
        }

        if ($alert === 'success_edit') {
            Swal.fire('修改成功', '', 'success').then(function () {
                setLocation();
            });
        }

        if ($alert === 'error') {
            Swal.fire('錯誤', '', 'error').then(function () {
                setLocation();
            });
        }

        if ($alert === 'error_addFail') {
            Swal.fire('新增失敗', '', 'error').then(function () {
                setLocation();
            });
        }

        if ($alert === 'error_delFail') {
            Swal.fire('刪除失敗', '', 'error').then(function () {
                setLocation();
            });
        }

        if ($alert === 'error_editFail') {
            Swal.fire('修改失敗', '', 'error').then(function () {
                setLocation();
            });
        }

        if ($alert === 'error_notFind') {
            Swal.fire('找不到該筆資料', '', 'error').then(function () {
                setLocation();
            });
        }

        if ($alert === 'error_noFunction') {
            Swal.fire('找不到處理方法', '', 'error').then(function () {
                setLocation();
            });
        }
    });
</script>

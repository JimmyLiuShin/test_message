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

        if ($alert === 'error') {
            Swal.fire('Error', '', 'error').then(function () {
                setLocation();
            });
        }
    });
</script>

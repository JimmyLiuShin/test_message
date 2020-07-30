<script src="https://code.jquery.com/jquery-3.5.1.min.js" type="application/javascript"></script>

<script>
    if ($success !== '' && $success) {
        alert($success);
    }
    if ($warning !== '' && $warning) {
        alert($warning);
    }

    function deleteMessage(id) {
        var verb = '確定要刪除嗎？';
        if (confirm(verb) === true && id > 0) {
            $('#FormDelete_' + id).submit();
        }
    }
</script>



<link rel="stylesheet" href="./style.css">

<script>
    $success = "<?php echo isset($_COOKIE['success']) ? $_COOKIE['success'] : ''; ?>";
    $warning = "<?php echo isset($_COOKIE['warning']) ? $_COOKIE['warning'] : ''; ?>";
</script>

<?php
if (isset($_COOKIE['success'])) {
    unset($_COOKIE['success']);
    setcookie('success', null, -1);
}
if (isset($_COOKIE['warning'])) {
    unset($_COOKIE['warning']);
    setcookie('warning', null, -1);
}
?>
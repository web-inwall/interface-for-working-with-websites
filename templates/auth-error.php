<?php include('header.php') ?>

<div class="d-flex flex-column align-items-center text-center">
    <h1>Ошибка. Неверный логин или пароль</h1>
    <a class="btn btn-primary" href="/">Вернуться на главную страницу</a>
</div>

<script>
    setTimeout(() => {
        location.href = '/';
    }, 5000);
</script>

<?php include('footer.php') ?>
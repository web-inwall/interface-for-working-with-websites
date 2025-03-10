<?php include('header.php') ?>

<div class="form-wrap d-flex align-items-center justify-content-center">
    <form method="POST" action="/">
        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Логин" name='username' required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" placeholder="Пароль" name='password' required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Войти</button>
    </form>
</div>

<?php include('footer.php') ?>
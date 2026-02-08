<?php $title = 'Login - ' . APP_NAME; ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title) ?></title>
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($error)): ?>
        <div style="color: red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/auth/login">
        <div>
            <label>Username: <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>"></label>
        </div>
        <div>
            <label>Password: <input type="password" name="password"></label>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
</body>
</html>
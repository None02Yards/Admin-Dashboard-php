<?php $title = 'Reset User Password - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Reset Password for <?= htmlspecialchars($user['username']) ?></h1>
    <p><a href="/listing/users">Back to users</a></p>

    <?php if (!empty($error)): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/useredit/resetPassword/<?= intval($user['id']) ?>">
        <?= Csrf::input() ?>
        <div>
            <label>New password: <input type="password" name="password"></label>
        </div>
        <div>
            <label>Confirm new password: <input type="password" name="password_confirm"></label>
        </div>
        <div><button type="submit">Reset password</button></div>
    </form>
</body>
</html>
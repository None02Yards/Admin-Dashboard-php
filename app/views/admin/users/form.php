<?php $title = 'Create User - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1><?= htmlspecialchars($title) ?></h1>
    <p><a href="/admin/users">Back to users</a></p>

    <?php if (!empty($error)): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/admin/userCreate">
        <?= Csrf::input() ?>
        <div>
            <label>Username: <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>"></label>
        </div>
        <div>
            <label>Password: <input type="password" name="password"></label>
        </div>
        <div>
            <label>Role:
                <select name="role">
                    <option value="voter" <?= (isset($role) && $role === 'voter') ? 'selected' : '' ?>>Voter</option>
                    <option value="admin" <?= (isset($role) && $role === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </label>
        </div>
        <div><button type="submit">Create user</button></div>
    </form>
</body>
</html>
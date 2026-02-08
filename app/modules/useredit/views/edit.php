<?php $title = 'Edit User - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Edit User</h1>
    <p><a href="/listing/users">Back to users</a></p>

    <?php if (!empty($error)): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/useredit/edit/<?= intval($user['id']) ?>">
        <?= Csrf::input() ?>
        <div>
            <label>Username: <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>"></label>
        </div>
        <div>
            <label>Role:
                <select name="role">
                    <option value="voter" <?= ($user['role'] === 'voter') ? 'selected' : '' ?>>Voter</option>
                    <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </label>
        </div>
        <div><button type="submit">Save</button></div>
    </form>
</body>
</html>
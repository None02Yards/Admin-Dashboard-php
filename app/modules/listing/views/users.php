<?php $title = 'Users (paginated) - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Users</h1>
    <p><a href="/admin/dashboard">Back to dashboard</a> | <a href="/admin/userCreate">Create new user</a></p>

    <form method="get" action="/listing/users">
        <label>Search username: <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"></label>
        <label>Per page:
            <select name="per_page">
                <?php foreach ([5,10,25,50] as $pp): ?>
                    <option value="<?= $pp ?>" <?= ($paginator->perPage == $pp) ? 'selected' : '' ?>><?= $pp ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Go</button>
    </form>

    <?php if (empty($rows)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th><th>Actions</th></tr>
            <?php foreach ($rows as $u): ?>
                <tr>
                    <td><?= intval($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
                    <td>
                        <a href="/useredit/edit/<?= intval($u['id']) ?>">Edit</a>
                        <a href="/useredit/resetPassword/<?= intval($u['id']) ?>">Reset password</a>
                        <?php if ($u['id'] != Auth::user()['id']): ?>
                        <form method="post" action="/admin/userDelete/<?= intval($u['id']) ?>" style="display:inline" onsubmit="return confirm('Delete this user?');">
                            <?= Csrf::input() ?>
                            <button type="submit">Delete</button>
                        </form>
                        <?php else: ?>
                            (current)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?= $paginator->renderLinks('/listing/users', ['q' => $q]) ?>
    <?php endif; ?>
</body>
</html>
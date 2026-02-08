<?php $title = 'Manage Positions - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Positions</h1>
    <p><a href="/admin/dashboard">Back to dashboard</a> | <a href="/admin/positionCreate">Create new position</a> | <a href="/auth/logout">Logout</a></p>

    <?php if (empty($positions)): ?>
        <p>No positions yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
            <?php foreach ($positions as $p): ?>
                <tr>
                    <td><?= intval($p['id']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>
                        <a href="/admin/positionEdit/<?= intval($p['id']) ?>">Edit</a>
                        <form method="post" action="/admin/positionDelete/<?= intval($p['id']) ?>" style="display:inline" onsubmit="return confirm('Delete this position? This will also delete related candidates and votes.');">
                            <?= Csrf::input() ?>
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
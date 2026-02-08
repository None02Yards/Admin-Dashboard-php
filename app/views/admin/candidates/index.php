<?php $title = 'Manage Candidates - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Candidates</h1>
    <p><a href="/admin/dashboard">Back to dashboard</a> | <a href="/admin/candidateCreate">Create new candidate</a> | <a href="/auth/logout">Logout</a></p>

    <?php if (empty($candidates)): ?>
        <p>No candidates yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Name</th><th>Position</th><th>Actions</th></tr>
            <?php foreach ($candidates as $c): ?>
                <tr>
                    <td><?= intval($c['id']) ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['position_name'] ?? ($positions[$c['position_id']]['name'] ?? '')) ?></td>
                    <td>
                        <a href="/admin/candidateEdit/<?= intval($c['id']) ?>">Edit</a>
                        <form method="post" action="/admin/candidateDelete/<?= intval($c['id']) ?>" style="display:inline" onsubmit="return confirm('Delete this candidate?');">
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
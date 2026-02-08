<?php $title = 'Admin Dashboard - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Admin Dashboard</h1>
    <p><a href="/auth/logout">Logout</a></p>
    <h2>Positions & vote counts</h2>
    <table border="1" cellpadding="6">
        <tr><th>Position</th><th>Total votes</th></tr>
        <?php foreach ($positions as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= intval($p['votes']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
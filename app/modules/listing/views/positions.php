<?php $title = 'Positions (paginated) - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Positions</h1>
    <p><a href="/admin/dashboard">Back to dashboard</a> | <a href="/admin/positionCreate">Create new position</a></p>

    <form method="get" action="/listing/positions">
        <label>Search: <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"></label>
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
        <p>No positions found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
            <?php foreach ($rows as $p): ?>
                <tr>
                    <td><?= intval($p['id']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>
                        <a href="/admin/positionEdit/<?= intval($p['id']) ?>">Edit</a>
                        <form method="post" action="/admin/positionDelete/<?= intval($p['id']) ?>" style="display:inline" onsubmit="return confirm('Delete this position?');">
                            <?= Csrf::input() ?>
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?= $paginator->renderLinks('/listing/positions', ['q' => $q]) ?>
    <?php endif; ?>
</body>
</html>
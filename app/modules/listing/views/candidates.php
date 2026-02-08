<?php $title = 'Candidates (paginated) - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1>Candidates</h1>
    <p><a href="/admin/dashboard">Back to dashboard</a> | <a href="/admin/candidateCreate">Create new candidate</a></p>

    <form method="get" action="/listing/candidates">
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
        <p>No candidates found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Name</th><th>Position</th><th>Actions</th></tr>
            <?php foreach ($rows as $c): ?>
                <tr>
                    <td><?= intval($c['id']) ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['position_name']) ?></td>
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

        <?= $paginator->renderLinks('/listing/candidates', ['q' => $q]) ?>
    <?php endif; ?>
</body>
</html>
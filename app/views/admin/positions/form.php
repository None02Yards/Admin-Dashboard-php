<?php $title = (isset($position) ? 'Edit Position' : 'Create Position') . ' - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1><?= htmlspecialchars($title) ?></h1>
    <p><a href="/admin/positions">Back to positions</a></p>

    <?php if (!empty($error)): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= isset($position) ? '/admin/positionEdit/' . intval($position['id']) : '/admin/positionCreate' ?>">
        <?= Csrf::input() ?>
        <div>
            <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($position['name'] ?? $name ?? '') ?>"></label>
        </div>
        <div><button type="submit"><?= isset($position) ? 'Save changes' : 'Create' ?></button></div>
    </form>
</body>
</html>
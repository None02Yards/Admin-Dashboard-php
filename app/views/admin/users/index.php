<?php $title = (isset($candidate) ? 'Edit Candidate' : 'Create Candidate') . ' - ' . APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1><?= htmlspecialchars($title) ?></h1>
    <p><a href="/admin/candidates">Back to candidates</a></p>

    <?php if (!empty($error)): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= isset($candidate) ? '/admin/candidateEdit/' . intval($candidate['id']) : '/admin/candidateCreate' ?>">
        <?= Csrf::input() ?>
        <div>
            <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($candidate['name'] ?? $name ?? '') ?>"></label>
        </div>
        <div>
            <label>Position:
                <select name="position_id">
                    <option value="">-- select --</option>
                    <?php foreach ($positions as $p): ?>
                        <?php $sel = ((isset($candidate) && $candidate['position_id']==$p['id']) || (isset($position_id) && $position_id==$p['id'])) ? 'selected' : '' ?>
                        <option value="<?= intval($p['id']) ?>" <?= $sel ?>><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div><button type="submit"><?= isset($candidate) ? 'Save changes' : 'Create' ?></button></div>
    </form>
</body>
</html>
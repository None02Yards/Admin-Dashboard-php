<?php $title = APP_NAME; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?= htmlspecialchars($title) ?></title></head>
<body>
    <h1><?= htmlspecialchars(APP_NAME) ?></h1>
    <p>Welcome, <?= htmlspecialchars(Auth::user()['username']) ?> — <a href="/auth/logout">Logout</a></p>

    <?php if (!empty($error)): ?>
        <div style="color: red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php foreach ($positions as $pos): ?>
        <section style="border:1px solid #ccc;padding:10px;margin:8px 0;">
            <h2><?= htmlspecialchars($pos['name']) ?></h2>
            <?php if (in_array($pos['id'], $votedRows)): ?>
                <div>You already voted for this position.</div>
            <?php else: ?>
                <form method="post" action="/vote/cast">
                    <input type="hidden" name="position_id" value="<?= intval($pos['id']) ?>">
                    <?php if (!empty($candidates[$pos['id']])): ?>
                        <?php foreach ($candidates[$pos['id']] as $cand): ?>
                            <div>
                                <label>
                                    <input type="radio" name="candidate_id" value="<?= intval($cand['id']) ?>">
                                    <?= htmlspecialchars($cand['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div><button type="submit">Submit Vote</button></div>
                    <?php else: ?>
                        <div>No candidates for this position yet.</div>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</body>
</html>
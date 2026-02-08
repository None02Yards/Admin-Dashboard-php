<?php
class VoteController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return $this->redirect('/auth/login');
        }
        $db = Database::getInstance();
        // Get positions and candidates
        $stmt = $db->query("SELECT * FROM positions ORDER BY id");
        $positions = $stmt->fetchAll();

        $candidatesStmt = $db->query("SELECT * FROM candidates ORDER BY position_id, id");
        $candidates = [];
        while ($r = $candidatesStmt->fetch()) {
            $candidates[$r['position_id']][] = $r;
        }

        // Check which positions user has already voted
        $user = Auth::user();
        $votedStmt = $db->prepare("SELECT position_id FROM votes WHERE user_id = ?");
        $votedStmt->execute([$user['id']]);
        $votedRows = $votedStmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $this->view('vote/index', compact('positions', 'candidates', 'votedRows'));
    }

    public function cast()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed.";
            exit;
        }

        if (!Auth::check()) {
            return $this->redirect('/auth/login');
        }

        $user = Auth::user();
        $position_id = intval($_POST['position_id'] ?? 0);
        $candidate_id = intval($_POST['candidate_id'] ?? 0);

        if (!$position_id || !$candidate_id) {
            $error = 'Invalid input.';
            return $this->view('vote/index', compact('error'));
        }

        $db = Database::getInstance();

        // Check if user already voted for this position
        $check = $db->prepare("SELECT COUNT(*) FROM votes WHERE user_id = ? AND position_id = ?");
        $check->execute([$user['id'], $position_id]);
        if ($check->fetchColumn() > 0) {
            $error = 'You have already voted for this position.';
            return $this->view('vote/index', compact('error'));
        }

        // Insert vote
        $ins = $db->prepare("INSERT INTO votes (user_id, position_id, candidate_id, created_at) VALUES (?, ?, ?, NOW())");
        $ins->execute([$user['id'], $position_id, $candidate_id]);

        $this->redirect('/');
    }
}
<?php
class AdminController extends Controller
{
    public function __construct()
    {
        // require admin for all actions in this controller
        Auth::requireRole(['admin']);
    }

    public function dashboard()
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT p.id, p.name, COUNT(v.id) as votes
                            FROM positions p
                            LEFT JOIN votes v ON v.position_id = p.id
                            GROUP BY p.id");
        $positions = $stmt->fetchAll();
        $this->view('admin/dashboard', compact('positions'));
    }

    //
    // Positions
    //
    public function positions()
    {
        $positionModel = new Position();
        $positions = $positionModel->all();
        $this->view('admin/positions/index', compact('positions'));
    }

    public function positionCreate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('admin/positions/form', compact('error'));
            }
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $error = 'Position name is required.';
                return $this->view('admin/positions/form', compact('error', 'name'));
            }
            $positionModel = new Position();
            $positionModel->create($name);
            $this->redirect('/admin/positions');
        }
        // GET
        $this->view('admin/positions/form');
    }

    public function positionEdit($id = null)
    {
        $id = intval($id);
        $positionModel = new Position();
        $position = $positionModel->find($id);
        if (!$position) {
            http_response_code(404);
            echo "Position not found.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('admin/positions/form', compact('error', 'position'));
            }
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $error = 'Position name is required.';
                return $this->view('admin/positions/form', compact('error', 'position'));
            }
            $positionModel->update($id, $name);
            $this->redirect('/admin/positions');
        }

        $this->view('admin/positions/form', compact('position'));
    }

    public function positionDelete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed.";
            exit;
        }
        if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
            $error = 'Invalid CSRF token.';
            echo $error;
            exit;
        }
        $id = intval($id);
        $positionModel = new Position();
        $positionModel->delete($id);
        $this->redirect('/admin/positions');
    }

    //
    // Candidates
    //
    public function candidates()
    {
        $candidateModel = new Candidate();
        $candidates = $candidateModel->allWithPosition();
        $positions = (new Position())->all();
        $this->view('admin/candidates/index', compact('candidates', 'positions'));
    }

    public function candidateCreate()
    {
        $positionModel = new Position();
        $positions = $positionModel->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('admin/candidates/form', compact('error', 'positions'));
            }
            $name = trim($_POST['name'] ?? '');
            $position_id = intval($_POST['position_id'] ?? 0);
            if ($name === '' || !$position_id) {
                $error = 'Candidate name and position are required.';
                return $this->view('admin/candidates/form', compact('error', 'positions', 'name', 'position_id'));
            }
            $candidateModel = new Candidate();
            $candidateModel->create($position_id, $name);
            $this->redirect('/admin/candidates');
        }

        $this->view('admin/candidates/form', compact('positions'));
    }

    public function candidateEdit($id = null)
    {
        $id = intval($id);
        $candidateModel = new Candidate();
        $candidate = $candidateModel->find($id);
        if (!$candidate) {
            http_response_code(404);
            echo "Candidate not found.";
            exit;
        }
        $positions = (new Position())->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('admin/candidates/form', compact('error', 'candidate', 'positions'));
            }
            $name = trim($_POST['name'] ?? '');
            $position_id = intval($_POST['position_id'] ?? 0);
            if ($name === '' || !$position_id) {
                $error = 'Candidate name and position are required.';
                return $this->view('admin/candidates/form', compact('error', 'candidate', 'positions'));
            }
            $candidateModel->update($id, $position_id, $name);
            $this->redirect('/admin/candidates');
        }

        $this->view('admin/candidates/form', compact('candidate', 'positions'));
    }

    public function candidateDelete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed.";
            exit;
        }
        if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
            echo 'Invalid CSRF token.';
            exit;
        }
        $id = intval($id);
        $candidateModel = new Candidate();
        $candidateModel->delete($id);
        $this->redirect('/admin/candidates');
    }

    //
    // User management (basic)
    //
    public function users()
    {
        $userModel = new User();
        $users = $userModel->all();
        $this->view('admin/users/index', compact('users'));
    }

    public function userCreate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('admin/users/form', compact('error'));
            }
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = in_array($_POST['role'] ?? 'voter', ['admin', 'voter']) ? $_POST['role'] : 'voter';

            if ($username === '' || $password === '') {
                $error = 'Username and password are required.';
                return $this->view('admin/users/form', compact('error', 'username', 'role'));
            }

            $userModel = new User();
            try {
                $userModel->createUser($username, $password, $role);
            } catch (Exception $e) {
                $error = $e->getMessage();
                return $this->view('admin/users/form', compact('error', 'username', 'role'));
            }

            $this->redirect('/admin/users');
        }

        $this->view('admin/users/form');
    }

    public function userDelete($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed.";
            exit;
        }
        if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
            echo 'Invalid CSRF token.';
            exit;
        }
        $id = intval($id);
        $userModel = new User();
        $userModel->delete($id);
        $this->redirect('/admin/users');
    }
}
<?php
class ListingController extends Controller
{
    public function __construct()
    {
        // only admin can access these listings in admin context
        Auth::requireRole(['admin']);
    }

    // /listing/positions
    public function positions()
    {
        $q = trim($_GET['q'] ?? '');
        $page = intval($_GET['page'] ?? 1);
        $perPage = intval($_GET['per_page'] ?? 10);

        $pdo = Database::getInstance();
        $paginator = new Paginator($page, $perPage);

        if ($q !== '') {
            $like = '%' . $q . '%';
            $countSql = "SELECT COUNT(*) FROM positions WHERE name LIKE :q";
            $dataSql = "SELECT * FROM positions WHERE name LIKE :q ORDER BY id ASC";
            $params = ['q' => $like];
        } else {
            $countSql = "SELECT COUNT(*) FROM positions";
            $dataSql = "SELECT * FROM positions ORDER BY id ASC";
            $params = [];
        }

        $rows = $paginator->paginateQuery($pdo, $countSql, $dataSql, $params);
        $this->view('modules/listing/positions', compact('rows', 'paginator', 'q'));
    }

    // /listing/candidates
    public function candidates()
    {
        $q = trim($_GET['q'] ?? '');
        $page = intval($_GET['page'] ?? 1);
        $perPage = intval($_GET['per_page'] ?? 10);

        $pdo = Database::getInstance();
        $paginator = new Paginator($page, $perPage);

        if ($q !== '') {
            $like = '%' . $q . '%';
            $countSql = "SELECT COUNT(*) FROM candidates WHERE name LIKE :q";
            $dataSql = "SELECT c.*, p.name as position_name FROM candidates c JOIN positions p ON c.position_id = p.id WHERE c.name LIKE :q ORDER BY p.id, c.id";
            $params = ['q' => $like];
        } else {
            $countSql = "SELECT COUNT(*) FROM candidates";
            $dataSql = "SELECT c.*, p.name as position_name FROM candidates c JOIN positions p ON c.position_id = p.id ORDER BY p.id, c.id";
            $params = [];
        }

        $rows = $paginator->paginateQuery($pdo, $countSql, $dataSql, $params);
        $this->view('modules/listing/candidates', compact('rows', 'paginator', 'q'));
    }

    // /listing/users
    public function users()
    {
        $q = trim($_GET['q'] ?? '');
        $page = intval($_GET['page'] ?? 1);
        $perPage = intval($_GET['per_page'] ?? 10);

        $pdo = Database::getInstance();
        $paginator = new Paginator($page, $perPage);

        if ($q !== '') {
            $like = '%' . $q . '%';
            $countSql = "SELECT COUNT(*) FROM users WHERE username LIKE :q";
            $dataSql = "SELECT id, username, role, created_at FROM users WHERE username LIKE :q ORDER BY id ASC";
            $params = ['q' => $like];
        } else {
            $countSql = "SELECT COUNT(*) FROM users";
            $dataSql = "SELECT id, username, role, created_at FROM users ORDER BY id ASC";
            $params = [];
        }

        $rows = $paginator->paginateQuery($pdo, $countSql, $dataSql, $params);
        $this->view('modules/listing/users', compact('rows', 'paginator', 'q'));
    }
}
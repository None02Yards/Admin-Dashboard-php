<?php
class UserEditController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['admin']);
    }

    // GET /useredit/edit/{id} and POST for saving username/role
    public function edit($id = null)
    {
        $id = intval($id);
        $userModel = new User();
        $user = $userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('modules/useredit/edit', compact('user', 'error'));
            }
            $username = trim($_POST['username'] ?? '');
            $role = in_array($_POST['role'] ?? 'voter', ['admin', 'voter']) ? $_POST['role'] : 'voter';

            if ($username === '') {
                $error = 'Username is required.';
                return $this->view('modules/useredit/edit', compact('user', 'error'));
            }

            try {
                $userModel->update($id, $username, $role);
                $this->redirect('/listing/users');
            } catch (Exception $e) {
                $error = $e->getMessage();
                return $this->view('modules/useredit/edit', compact('user', 'error'));
            }
        }

        $this->view('modules/useredit/edit', compact('user'));
    }

    // GET /useredit/resetPassword/{id} and POST to set a new password
    public function resetPassword($id = null)
    {
        $id = intval($id);
        $userModel = new User();
        $user = $userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate($_POST[Csrf::TOKEN_KEY] ?? '')) {
                $error = 'Invalid CSRF token.';
                return $this->view('modules/useredit/reset_password', compact('user', 'error'));
            }
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if ($password === '' || $passwordConfirm === '') {
                $error = 'Both password fields are required.';
                return $this->view('modules/useredit/reset_password', compact('user', 'error'));
            }
            if ($password !== $passwordConfirm) {
                $error = 'Passwords do not match.';
                return $this->view('modules/useredit/reset_password', compact('user', 'error'));
            }
            if (strlen($password) < 6) {
                $error = 'Password should be at least 6 characters.';
                return $this->view('modules/useredit/reset_password', compact('user', 'error'));
            }

            $userModel->updatePassword($id, $password);
            $this->redirect('/listing/users');
        }

        $this->view('modules/useredit/reset_password', compact('user'));
    }
}
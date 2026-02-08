<?php
class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$username || !$password) {
                $error = 'Username and password are required.';
                return $this->view('auth/login', compact('error', 'username'));
            }

            $userModel = new User();
            $user = $userModel->findByUsername($username);
            if ($user && password_verify($password, $user['password_hash'])) {
                Auth::login($user);
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $this->redirect('/admin/dashboard');
                } else {
                    $this->redirect('/');
                }
            } else {
                $error = 'Invalid credentials.';
                return $this->view('auth/login', compact('error', 'username'));
            }
        }

        // GET
        $this->view('auth/login');
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/auth/login');
    }
}
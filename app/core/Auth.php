<?php
// Simple auth helper for session based login and role checks
class Auth
{
    public static function login($user)
    {
        // $user array must contain id, username, role
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
    }

    public static function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', time() - 42000);
        }
        session_destroy();
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check()
    {
        return (bool) self::user();
    }

    public static function requireRole($roles = [])
    {
        $user = self::user();
        if (!$user || !in_array($user['role'], (array)$roles)) {
            http_response_code(403);
            echo "Forbidden: insufficient permissions.";
            exit;
        }
    }
}
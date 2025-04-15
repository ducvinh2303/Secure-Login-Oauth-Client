<?php
include_once '../config/app.php';

if (!empty($TIMEZONE)) {
    date_default_timezone_set($TIMEZONE);
} else {
    // Set a default timezone or handle the error
    date_default_timezone_set('UTC');
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// session_start();

class Controller
{
    public function view($view)
    {
        header("Location: " . $view);
        exit;
    }

    public function back()
    {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function successMessage(string $message = "Successful manipulation !")
    {
        $_SESSION['success'] = $message;
    }

    public function errorMessage(string $message = "Failed manipulation !")
    {
        $_SESSION['error'] = $message;
    }
}

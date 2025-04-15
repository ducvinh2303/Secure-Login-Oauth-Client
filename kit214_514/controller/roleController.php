<?php
include_once "controller.php";

class RoleController extends Controller
{
    protected $ROLE_ADMIN = "ADMIN";
    protected $ROLE_OTHER;

    public function __construct()
    {
    }

    public function isLogged()
    {
        return isset($_SESSION["user"]);
    }

    public function isAdmin()
    {
        if (isset($_SESSION["user"])) {
            return $_SESSION["user"]['role_code'] === $this->ROLE_ADMIN;
        }
        return false;
    }

    public function isAuthor($blog)
    {
        if (isset($_SESSION["user"])) {
            return $_SESSION["user"]['id'] === $blog['created_by'];
        }
        return false;
    }
}

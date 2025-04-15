<?php
include_once "controller.php";
include_once "../model/userModel.php";

class PermissionController extends Controller
{
    public function __construct()
    {
    }

    public function userList()
    {
        try {
            // Initialize UserModel object
            $userModel = new UserModel();
            // Use userModel to query to get all users
            $list = $userModel->sql('SELECT * FROM users');
        } catch (\Throwable $th) {
            return [];
        }
        return $list;
    }

    public function changeRole($request)
    {
        try {
            // Initialize UserModel object
            $userModel = new UserModel();

            // Get the posted values
            $newRole = $request['new_role'];
            $userId = $request['user_id'];

            // Create the data array to be updated
            $data = [
                'role_code' => $newRole,
            ];

            // Perform new role update for user
            $userModel->update($data, ["id" => $userId]); 

            $this->successMessage("Change role success");
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
            $this->errorMessage("Change role failed");
            // echo $th->getMessage(); // view error
            return;
        }

        // Success login then redirect to login page
        return $this->view('permission');
    }
}
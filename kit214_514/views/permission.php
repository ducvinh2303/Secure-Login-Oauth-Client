<?php
// If you are logged in, you cannot access it
// Load controllers to use in the page
include_once(__DIR__ . "/../controller/logsController.php");
include_once(__DIR__ . "/../controller/permissionController.php");
include_once "../controller/userController.php";

// create new controller
$logsController = new LogsController();
$permissionController = new PermissionController();

// invalid when access this page
$state = 1;

// Get the current role code of the logged in user
$roleCode = $_SESSION["user"]["role_code"];

// Call the getList function to get all users.
$user_list = $permissionController->userList();

// Check if there is login and role is ADMIN then status is valid
if (isset($_SESSION["user"]) and $roleCode === "ADMIN") {
    $state = 0;
}

// Call access log function
$logsController->writeLogs($state);

// Check if not logged in then return to login page
if (!isset($_SESSION["user"])) {
    header("Location: login");
    exit;
}

// Check if the account is not ADMIN role then return to Home page
if ($roleCode !== "ADMIN") {
    header("Location: home");
    exit;
}

// Check if it is a POST action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if there are user_id and new_role variables then call changeRole function
    if (isset($_POST['user_id']) && isset($_POST['new_role'])) {
        // handle change role
        $permissionController->changeRole($_POST);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission</title>
    <link rel="stylesheet" href="public/styles/menu.css">
    <link rel="stylesheet" href="public/styles/permission.css">
</head>

<body>
    <div style="display: flex; flex-direction: column;">
        <?php include 'components/menu_home.php'; ?>
        <div class="flex flex-col gap-5 p-10">
            <div class="text-[20px] font-bold">Permission Management</div>
            <div>
                <div class="table w-full table-fixed">
                    <div class="table-header-group w-full bg-slate-500 font-semibold">
                        <div class="table-row divide-x divide-solid divide-white">
                            <div class="table-cell h-10 w-[350px] px-3 text-center align-middle">
                                <div class="text-white">Username</div>
                            </div>
                            <div class="table-cell h-10 w-[130px] px-3 text-center align-middle">
                                <div class="text-white">IP</div>
                            </div>
                            <div class="table-cell h-10 px-3 text-center align-middle">
                                <div class="text-white">Location</div>
                            </div>
                            <div class="table-cell h-10 px-3 text-center align-middle">
                                <div class="text-white">Role Code</div>
                            </div>
                            <div class="table-cell h-10 w-[150px] px-3 text-center align-middle">
                                <div class="text-white">Change role</div>
                            </div>
                        </div>
                    </div>
                    <div class="table-footer-group">
                        <?php foreach ($user_list as $user): ?>
                            <div class="table-row cursor-pointer divide-x divide-solid divide-white transition-all odd:bg-slate-300 even:bg-slate-400 hover:bg-slate-400/60">
                                <div class="table-cell p-2 align-middle">
                                    <div class="flex items-center justify-center">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </div>
                                </div>
                                <div class="table-cell p-2 align-middle">
                                    <div class="flex items-center justify-center">
                                        <?php echo htmlspecialchars($user['ip']); ?>
                                    </div>
                                </div>
                                <div class="table-cell p-2 align-middle">
                                    <div class="flex items-center justify-center">
                                        <?php echo htmlspecialchars($user['location']); ?>
                                    </div>
                                </div>
                                <div class="table-cell p-2 align-middle">
                                    <div class="flex items-center justify-center">
                                        <?php echo htmlspecialchars($user['role_code']); ?>
                                    </div>
                                </div>
                                <div class="table-cell p-2 text-center align-middle">
                                    <form action="" method="post" id="form_role_<?php echo $user['id']; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select
                                            name="new_role"
                                            class="w-full py-0.5"
                                            <?php echo $user['role_code'] === 'ADMIN' ? 'disabled' : ''; ?>
                                            onchange="document.getElementById('form_role_<?php echo $user['id']; ?>').submit();">
                                            <option value="">Select role</option>
                                            <option value="BASIC" <?php echo $user['role_code'] === 'BASIC' ? 'selected' : ''; ?>>BASIC</option>
                                            <option value="MODERATOR" <?php echo $user['role_code'] === 'MODERATOR' ? 'selected' : ''; ?>>MODERATOR</option>
                                            <option value="ADMIN" <?php echo $user['role_code'] === 'ADMIN' ? 'selected' : ''; ?>>ADMIN</option>
                                        </select>
                                    </form>
                                    <!-- <select 
                                        name="" 
                                        id="" 
                                        class="w-full py-0.5"
                                        <?php echo $user['role_code'] === 'ADMIN' ? 'disabled' : ''; ?>
                                    >
                                        <option value="" selected>Select role</option>
                                        <option value="BASIC">BASIC</option>
                                        <option value="MODERATOR">MODERATOR</option>
                                        <option value="ADMIN">ADMIN</option>
                                    </select> -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
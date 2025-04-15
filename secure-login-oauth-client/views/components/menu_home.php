<?php
// use declarative modules
include_once(__DIR__ . "/../../controller/userController.php");
include_once(__DIR__ . "/../../controller/controller.php");

// create new controller
$userController = new UserController();

// Check if it is a POST action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if logout variable exists and if logout variable has ok value
  if (isset($_POST["logout"]) && $_POST["logout"] === "ok") {
    // If it is a logout action then logout is performed.
    $userController->logout();
  }
}

// Get the role code of the logged in user
$roleCode = $_SESSION["user"]["role_code"];
?>

<div class="flex h-[80px] items-center justify-between bg-gray-200 px-6">
  <div class="flex items-center gap-1">
    <a href="home" class="cursor-pointer rounded-lg px-4 py-3 transition-all hover:bg-blue-200 menu-header <?php echo (strpos($_SERVER['REQUEST_URI'], "/home") !== false || $_SERVER['REQUEST_URI'] === '/web_oauth/') ? "active" : "" ?>">
      <div class="text font-semibold text-gray-800">Home</div>
    </a>
    <!-- Check if it is ADMIN role then display Permission menu -->
    <?php if ($roleCode === 'ADMIN'): ?>
      <a href="permission" class="cursor-pointer rounded-lg px-4 py-3 transition-all hover:bg-blue-200 menu-header <?php echo (strpos($_SERVER['REQUEST_URI'], "/permission") !== false) ? "active" : "" ?>">
        <div class="text font-semibold text-gray-800">Permission</div>
      </a>
    <?php endif; ?>
    <!-- Check if it is ADMIN or MODERATOR role then the Access Log menu will be displayed. -->
    <?php if ($roleCode === 'MODERATOR' || $roleCode === 'ADMIN'): ?>
      <a href="access-log" class="cursor-pointer rounded-lg px-4 py-3 transition-all hover:bg-blue-200 menu-header <?php echo (strpos($_SERVER['REQUEST_URI'], "/access-log") !== false) ? "active" : "" ?>">
        <div class="text font-semibold text-gray-800">Access Log</div>
      </a>
    <?php endif; ?>
    <a href="discord-account-link" class="cursor-pointer rounded-lg px-4 py-3 transition-all hover:bg-blue-200 menu-header <?php echo (strpos($_SERVER['REQUEST_URI'], "/discord-account-link") !== false) ? "active" : "" ?>">
      <div class="text font-semibold text-gray-800">Discord Account Link</div>
    </a>
  </div>
  <div class="flex">
    <form action="" method="post">
      <button type="submit" name="logout" value="ok" class="cursor-pointer rounded-lg bg-blue-200 px-4 py-3 transition-all hover:bg-blue-300">
        <div class="text font-semibold text-gray-800">Logout</div>
      </button>
    </form>
  </div>
</div>
<?php
// If you are logged in, you cannot access it
include_once(__DIR__ . "/../controller/logsController.php");

// create new controller
$logsController = new LogsController();

// invalid when access this page
$state = 1;

// set state valid when user loged
// If logged in then status is valid
if (isset($_SESSION["user"])) {
    $state = 0;
}

// handle write log to database
$logsController->writeLogs($state);

// redirect to login when non-user-loged
// Call the access log function
$logsController->writeLogs($state);

// Check if not logged in then return to login page
if (!isset($_SESSION["user"])) {
    header("Location: login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="public/styles/menu.css">
</head>

<body>
    <div style="display: flex; flex-direction: column;">
        <?php include 'components/menu_home.php'; ?>
        <div>
            Home page
        </div>
    </div>
</body>

</html>
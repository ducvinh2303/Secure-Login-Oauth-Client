<?php
// use declarative modules
include_once(__DIR__ . "/../controller/logsController.php");

// create new controller
$logsController = new LogsController();

// invalid when access this page
$state = 1;

// if (isset($_SESSION["user"])) {
//     $state = 0;
// }

// handle write log to database
$logsController->writeLogs($state);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div style="padding: 30px">
        <div style="font-size: 20px; font-weight: bold;">
            Error 403 Forbidden
        </div>
        <div style="padding-top: 15px">
            <a href="home">Home Page</a>
        </div>
    </div>
</body>

</html>
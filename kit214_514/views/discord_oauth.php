<?php
// use declarative modules
include_once(__DIR__ . "/../controller/userController.php");
include_once(__DIR__ . "/../controller/logsController.php");

// create new controller
$logsController = new LogsController();
$userController = new UserController();
// invalid when access this page
$state = 1; 

// set state valid when validate parameter
if ($userController->validateOAuth($_GET)) {
    $state = 0;
    // handle oauth discord
    $userController->handleDiscordOauth($_GET); // handle create user
}

// handle write log to database
$logsController->writeLogs($state);

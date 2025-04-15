<?php
// use declarative modules
// load into logsController
include_once(__DIR__ . "/../controller/logsController.php");
// create new controller 
$logsController = new LogsController();

// Get the role code of the logged in user
$roleCode = $_SESSION["user"]["role_code"];

// Just access the page and call the function to get all the recorded logs of all ips.
$listLogs = $logsController->getLog($_POST);

// invalid when access this page
$state = 1;
// check status valid when access this page
if (isset($_SESSION["user"]) && ($roleCode === "ADMIN" || $roleCode === "MODERATOR")) {
    $state = 0;
}

// handle write log to database
$logsController->writeLogs($state);

// Check if not logged in return to Login page
if (!isset($_SESSION["user"])) {
    header("Location: login");
    exit;
}

// Check if user is not ADMIN or MODERATOR role then return to home page
if ($roleCode !== "ADMIN" && $roleCode !== "MODERATOR") {
    header("Location: home");
    exit;
}

// // Check if it is a POST action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the ip_search variable exists, if so, call the getLog function to get the logs of the searched ip.
    if (isset($_POST['ip_search'])) {
        // call method get log
        $listLogs = $logsController->getLog($_POST);
    }
}

// var_dump($listLogs["jsonData"]);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Log</title>
    <link rel="stylesheet" href="public/styles/menu.css">
    <link rel="stylesheet" href="public/styles/access-log.css">
</head>

<body>
    <div style="display: flex; flex-direction: column;">
        <?php include 'components/menu_home.php'; ?>
        <div class="flex flex-col gap-5 p-10">
            <div class="text-[20px] font-bold">Access Log</div>
            <div class="flex flex-col gap-5">
                <div class="flex items-center gap-10">
                    <form action="" method="post" id="form_search">
                        <div class="flex">
                            <div>
                                <input type="text" name="ip_search" class="w-[180px] rounded-bl-md rounded-tl-md border border-solid border-gray-300 px-3 py-1.5 outline-0 ring-0 ring-offset-0 transition-all focus:border-gray-500 focus:ring-0 focus:ring-offset-0" placeholder="Enter ip to search" />
                            </div>
                            <div>
                                <button
                                    class="rounded-br-md rounded-tr-md bg-slate-500 px-4 py-[7px] text-white transition-all hover:bg-slate-600"
                                    type="submit">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="flex items-center gap-3">
                        <div class="font-bold">Display Type:</div>
                        <div>
                            <select name="display_type" id="select-display-type" class="w-[180px] border border-solid border-gray-400 px-2 py-0.5">
                                <option value="table" selected>Table</option>
                                <option value="ulli">Ul & li</option>
                                <option value="json">Json</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <div id="table-display">
                        <div class="table w-full table-fixed">
                            <div class="table-header-group w-full bg-slate-500 font-semibold">
                                <div class="table-row divide-x divide-solid divide-white">
                                    <div class="table-cell h-10 w-[180px] px-3 text-center align-middle">
                                        <div class="text-white">IP</div>
                                    </div>
                                    <div class="table-cell h-10 px-3 text-center align-middle">
                                        <div class="text-white">Path</div>
                                    </div>
                                    <div class="table-cell h-10 w-[150px] px-3 text-center align-middle">
                                        <div class="text-white">State</div>
                                    </div>
                                    <div class="table-cell h-10 w-[100px] px-3 text-center align-middle">
                                        <div class="text-white">User ID</div>
                                    </div>
                                    <div class="table-cell h-10 w-[250px] px-3 text-center align-middle">
                                        <div class="text-white">Date</div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-footer-group">
                                <?php foreach ($listLogs["data"] as $item): ?>
                                    <div class="table-row cursor-pointer divide-x divide-solid divide-white transition-all odd:bg-slate-300 even:bg-slate-400 hover:bg-slate-400/60">
                                        <div class="table-cell p-2 align-middle">
                                            <div class="flex items-center justify-center">
                                                <?php echo htmlspecialchars($item['ip']); ?>
                                            </div>
                                        </div>
                                        <div class="table-cell p-2 align-middle">
                                            <div class="flex items-center justify-center">
                                                <?php echo htmlspecialchars($item['path']); ?>
                                            </div>
                                        </div>
                                        <div class="table-cell p-2 align-middle">
                                            <div class="flex items-center justify-center">
                                                <?php echo htmlspecialchars($item['state'] === 0 ? 'Valid' : 'Invalid'); ?>
                                            </div>
                                        </div>
                                        <div class="table-cell p-2 align-middle">
                                            <div class="flex items-center justify-center">
                                                <?php echo htmlspecialchars(!$item['user_id'] ? "" : $item['user_id']); ?>
                                            </div>
                                        </div>
                                        <div class="table-cell p-2 text-center align-middle">
                                            <div class="flex items-center justify-center">
                                                <?php echo htmlspecialchars($item['date']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div id="list-display">
                        <ul class="list-disc">
                            <?php foreach ($listLogs["data"] as $item): ?>
                                <li>
                                    <span><strong>IP:</strong> <?php echo htmlspecialchars($item['ip']); ?></span> | <span><strong>Path:</strong> <?php echo htmlspecialchars($item['path']); ?></span> | <span><strong>State:</strong> <?php echo htmlspecialchars($item['state'] === 0 ? 'Valid' : 'Invalid'); ?></span> | <span><strong>User ID:</strong> <?php echo htmlspecialchars(!$item['user_id'] ? "" : $item['user_id']); ?></span> | <span><strong>Date:</strong> <?php echo htmlspecialchars($item['date']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div id="json-display">
                        <div>
                            <?php echo htmlspecialchars($listLogs["jsonData"]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/access-log.js"></script>
</body>

</html>
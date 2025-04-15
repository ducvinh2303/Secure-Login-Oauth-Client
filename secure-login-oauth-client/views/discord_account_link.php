<?php
// use declarative modules
include_once(__DIR__ . "/../controller/discordController.php");
include_once(__DIR__ . "/../controller/logsController.php");
// create new controller
$logsController = new LogsController();
$discordController = new DiscordController();

// invalid when access this page
$state = 1; 

// set state valid when user loged
if (isset($_SESSION["user"])) {
    $state = 0;
}

// handle write log to database
$logsController->writeLogs($state);

// If you are logged in, you cannot access it
if (!isset($_SESSION["user"])) {
    header("Location: login");
    exit;
}

// handle get data from method profile
$profile = $discordController->profile();

// handle when profile have access token
if (isset($profile["access_token"])) {
    // get data user and guilds
    $user = $discordController->user();
    $guilds = $discordController->guilds();
}

// handle use csrf for login discord
$_SESSION["csrf"] = base64_encode(time());

// create variable for state
$state = [
    "action" => "link_account",
    "csrf" => $_SESSION["csrf"]
];

// handle encrypt state
$state_base64 = base64_encode(json_encode($state));

// variable for error and success
$error = "";
$success = "";
if (isset($_SESSION["error"])) {
    $error = $_SESSION["error"];
    unset($_SESSION["error"]);
}
if (isset($_SESSION["success"])) {
    $success = $_SESSION["success"];
    unset($_SESSION["success"]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discord Account Link</title>
    <link rel="stylesheet" href="public/styles/menu.css">
</head>

<body>
    <div style="display: flex; flex-direction: column;">
        <?php include 'components/menu_home.php'; ?>
        <div style="padding: 10px;">
            <?php if (isset($profile["access_token"])) { ?>
                <img src="https://cdn.discordapp.com/avatars/<?php echo $user->user->id; ?>/<?php echo $user->user->avatar; ?>" />
                Username: <?php echo $user->user->username; ?>

                <hr>
                <h2 style="font-weight: bold; font-size: 24px;">
                    List guilds
                </h2>
                <br>
                <?php foreach ($guilds as $item) { ?>
                    <div>
                        Name: <?php echo $item->name; ?>
                    </div>
                <?php } ?>
            <?php } else {  ?>

                <?php if (isset($error) and $error !== "") { ?>
                    <div class="flex items-center p-4 mt-8 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-700" role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ms-3 text-sm font-medium">
                            <?php echo $error ?>
                        </div>
                    </div>
                <?php } ?>

                Link account discord: <a href="https://discord.com/oauth2/authorize?client_id=<?php echo urlencode($_ENV["DISCORD_CLIENT_ID"]); ?>&response_type=code&redirect_uri=<?php echo urlencode($_ENV["DISCORD_REDIRECT_URI"]); ?>&scope=identify+guilds&state=<?php echo $state_base64; ?>">
                    <svg width="24px" height="24px" viewBox="0 -28.5 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid">
                        <g>
                            <path d="M216.856339,16.5966031 C200.285002,8.84328665 182.566144,3.2084988 164.041564,0 C161.766523,4.11318106 159.108624,9.64549908 157.276099,14.0464379 C137.583995,11.0849896 118.072967,11.0849896 98.7430163,14.0464379 C96.9108417,9.64549908 94.1925838,4.11318106 91.8971895,0 C73.3526068,3.2084988 55.6133949,8.86399117 39.0420583,16.6376612 C5.61752293,67.146514 -3.4433191,116.400813 1.08711069,164.955721 C23.2560196,181.510915 44.7403634,191.567697 65.8621325,198.148576 C71.0772151,190.971126 75.7283628,183.341335 79.7352139,175.300261 C72.104019,172.400575 64.7949724,168.822202 57.8887866,164.667963 C59.7209612,163.310589 61.5131304,161.891452 63.2445898,160.431257 C105.36741,180.133187 151.134928,180.133187 192.754523,160.431257 C194.506336,161.891452 196.298154,163.310589 198.110326,164.667963 C191.183787,168.842556 183.854737,172.420929 176.223542,175.320965 C180.230393,183.341335 184.861538,190.991831 190.096624,198.16893 C211.238746,191.588051 232.743023,181.531619 254.911949,164.955721 C260.227747,108.668201 245.831087,59.8662432 216.856339,16.5966031 Z M85.4738752,135.09489 C72.8290281,135.09489 62.4592217,123.290155 62.4592217,108.914901 C62.4592217,94.5396472 72.607595,82.7145587 85.4738752,82.7145587 C98.3405064,82.7145587 108.709962,94.5189427 108.488529,108.914901 C108.508531,123.290155 98.3405064,135.09489 85.4738752,135.09489 Z M170.525237,135.09489 C157.88039,135.09489 147.510584,123.290155 147.510584,108.914901 C147.510584,94.5396472 157.658606,82.7145587 170.525237,82.7145587 C183.391518,82.7145587 193.761324,94.5189427 193.539891,108.914901 C193.539891,123.290155 183.391518,135.09489 170.525237,135.09489 Z" fill="#5865F2" fill-rule="nonzero">

                            </path>
                        </g>
                    </svg>
                </a>
            <?php }  ?>
        </div>
    </div>

    <style>
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .ms-3 {
            margin-inline-start: 0.75rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .flex {
            display: flex;
        }

        .h-4 {
            height: 1rem;
        }

        .w-4 {
            width: 1rem;
        }

        .flex-shrink-0 {
            flex-shrink: 0;
        }

        .items-center {
            align-items: center;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .border {
            border-width: 1px;
        }

        .border-red-700 {
            --tw-border-opacity: 1;
            border-color: rgb(185 28 28 / var(--tw-border-opacity));
        }

        .bg-gray-800 {
            --tw-bg-opacity: 1;
            background-color: rgb(31 41 55 / var(--tw-bg-opacity));
        }

        .bg-red-50 {
            --tw-bg-opacity: 1;
            background-color: rgb(254 242 242 / var(--tw-bg-opacity));
        }

        .p-4 {
            padding: 1rem;
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .font-medium {
            font-weight: 500;
        }

        .text-red-400 {
            --tw-text-opacity: 1;
            color: rgb(248 113 113 / var(--tw-text-opacity));
        }

        .text-red-800 {
            --tw-text-opacity: 1;
            color: rgb(153 27 27 / var(--tw-text-opacity));
        }

        @media (prefers-color-scheme: dark) {
            .dark\:bg-gray-800 {
                --tw-bg-opacity: 1;
                background-color: rgb(31 41 55 / var(--tw-bg-opacity));
            }

            .dark\:text-red-400 {
                --tw-text-opacity: 1;
                color: rgb(248 113 113 / var(--tw-text-opacity));
            }
        }
    </style>
</body>

</html>
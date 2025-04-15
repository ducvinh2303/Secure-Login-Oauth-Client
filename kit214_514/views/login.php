<?php
// use declarative modules
include_once(__DIR__ . "/../controller/userController.php");
include_once(__DIR__ . "/../controller/logsController.php");

// create new controller
$userController = new UserController();
$logsController = new LogsController();
$state = 1; // invalid when access this page

// set state valid when user non-user-loged
if (!isset($_SESSION["user"])) {
    $state = 0;
}

// handle create log to database
$logsController->writeLogs($state);

// If you are logged in, you cannot access it
if (isset($_SESSION["user"])) {
    header("Location: home");
    exit;
}

// when method is POST || create new user
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // check validation parameters required
    if ($userController->validateRequestLogin($_POST)) {
        // handle call method login
        $userController->login($_POST);
    }
}

// create variable for error and success
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

// create variable for csrf
$_SESSION["csrf"] = base64_encode(time());

// create variable stored state
$state = [
    "action" => "login",
    "csrf" => $_SESSION["csrf"]
];

// encrypt state to base64
$state_base64 = base64_encode(json_encode($state));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="public/styles/register-login.css">
</head>

<body>
    <div class="flex flex-col items-center bg-gray-200 h-screen py-10">
        <div class="max-w-md bg-gray-100 w-full px-8 py-8 rounded-2xl">
            <div class="text-2xl font-bold">
                Login to website
            </div>
            <div class="font-semibold text-gray-400">Login to access my website</div>

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

            <?php if (isset($success) and $success !== "") { ?>
                <div class="flex items-center p-4 mt-8 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-700" role="alert">
                    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div class="ms-3 text-sm font-medium">
                        <?php echo $success ?>
                    </div>
                </div>
            <?php } ?>

            <form action="" method="post">
                <div class="mt-8 flex flex-col gap-1">
                    <div class="font-semibold">Username</div>
                    <div class="">
                        <input type="text" class="bg-white w-full h-10 border border-solid border-gray-200 rounded outline-none focus:ring-2 ring-sky-400 px-3" required name="username" value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="mt-3 flex flex-col gap-1">
                    <div class="font-semibold">Password</div>
                    <div class="">
                        <input type="password" class="bg-white w-full h-10 border border-solid border-gray-200 rounded outline-none focus:ring-2 ring-sky-400 px-3" required name="password" value="<?= htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="mt-5">
                    <button class="bg-sky-500 hover:bg-sky-600 active:bg-sky-700 text-white w-full h-10 rounded-md font-bold">Login</button>
                </div>
            </form>

            <div class="mt-3 text-center font-semibold text-gray-600">Don't have account? <a href="./register" class="text-sky-600 hover:text-sky-700 active:text-sky-800">Register Now.</a></div>

            <div class="mt-20 text-center font-semibold text-gray-600">Or Sign In with</div>
            <div class="mt-5 flex justify-center">
                <div class="bg-white size-10 grid place-items-center border border-solid border-gray-300 rounded">
                    <a href="https://discord.com/oauth2/authorize?client_id=<?php echo urlencode($_ENV["DISCORD_CLIENT_ID"]); ?>&response_type=code&redirect_uri=<?php echo urlencode($_ENV["DISCORD_REDIRECT_URI"]); ?>&scope=identify+guilds&state=<?php echo $state_base64; ?>">
                        <svg width="24px" height="24px" viewBox="0 -28.5 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid">
                            <g>
                                <path d="M216.856339,16.5966031 C200.285002,8.84328665 182.566144,3.2084988 164.041564,0 C161.766523,4.11318106 159.108624,9.64549908 157.276099,14.0464379 C137.583995,11.0849896 118.072967,11.0849896 98.7430163,14.0464379 C96.9108417,9.64549908 94.1925838,4.11318106 91.8971895,0 C73.3526068,3.2084988 55.6133949,8.86399117 39.0420583,16.6376612 C5.61752293,67.146514 -3.4433191,116.400813 1.08711069,164.955721 C23.2560196,181.510915 44.7403634,191.567697 65.8621325,198.148576 C71.0772151,190.971126 75.7283628,183.341335 79.7352139,175.300261 C72.104019,172.400575 64.7949724,168.822202 57.8887866,164.667963 C59.7209612,163.310589 61.5131304,161.891452 63.2445898,160.431257 C105.36741,180.133187 151.134928,180.133187 192.754523,160.431257 C194.506336,161.891452 196.298154,163.310589 198.110326,164.667963 C191.183787,168.842556 183.854737,172.420929 176.223542,175.320965 C180.230393,183.341335 184.861538,190.991831 190.096624,198.16893 C211.238746,191.588051 232.743023,181.531619 254.911949,164.955721 C260.227747,108.668201 245.831087,59.8662432 216.856339,16.5966031 Z M85.4738752,135.09489 C72.8290281,135.09489 62.4592217,123.290155 62.4592217,108.914901 C62.4592217,94.5396472 72.607595,82.7145587 85.4738752,82.7145587 C98.3405064,82.7145587 108.709962,94.5189427 108.488529,108.914901 C108.508531,123.290155 98.3405064,135.09489 85.4738752,135.09489 Z M170.525237,135.09489 C157.88039,135.09489 147.510584,123.290155 147.510584,108.914901 C147.510584,94.5396472 157.658606,82.7145587 170.525237,82.7145587 C183.391518,82.7145587 193.761324,94.5189427 193.539891,108.914901 C193.539891,123.290155 183.391518,135.09489 170.525237,135.09489 Z" fill="#5865F2" fill-rule="nonzero">

                                </path>
                            </g>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
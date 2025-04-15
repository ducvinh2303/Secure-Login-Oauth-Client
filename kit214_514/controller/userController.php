<?php
include_once "controller.php";
include_once "../model/userModel.php";
include_once "../model/discordOAuthModel.php";

class UserController extends Controller
{

    public function __construct() {}

    // handle login user
    public function login($request)
    {
        try {
            $userModel = new UserModel();

            $data = [
                'username' => $request['username']
            ];

            $user = $userModel->first($data);

            $password = $request['password'];

            if (!$user) {
                $this->errorMessage("Account or password is incorrect");
                return;
            }

            $salt = $user["salt"];
            $hashPassword = $user["password"];

            // login by password and salt, compare
            if (md5($password . $salt) !== $hashPassword) {
                $this->errorMessage("Account or password is incorrect");
                return;
            }

            $_SESSION['user'] = $user;
        } catch (\Throwable $th) {
            $this->errorMessage("Login failed, server error");
            // echo $th->getMessage(); // view error
            return;
        }

        return $this->view('home');
    }

    // handle create user when register
    public function createUser($request)
    {
        try {
            $userModel = new UserModel();

            $username = $request['username'];
            $password = $request['password'];
            // create salt encrypt
            $salt = md5(time());
            // handle hash password by password and salt
            $hashPassword = md5($password . $salt);

            $ip = "";
            $location = "";

            try {
                // handle get ip user
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://ip-to-location1.p.rapidapi.com/myip?ip=' . $_SERVER['REMOTE_ADDR'], [
                    'headers' => [
                        'x-rapidapi-host' => 'ip-to-location1.p.rapidapi.com',
                        'x-rapidapi-key'     => 'f8df6ef829msh0c01c266522739cp12fac0jsn4c14924fd26e',
                        'Accept'     => 'application/json',
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                $ip = $data["ip"];
                $location = $data["geo"]["city"];
            } catch (Exception $e) {
            }

            $data = [
                'username' => $username,
                'password' => $hashPassword,
                'salt' => $salt,
                'ip' => $ip,
                'location' => $location
            ];

            // Check duplicate usernames
            if ($userModel->first(['username' => $data['username']])) {
                $this->errorMessage("This username is already taken, please replace it");
                return;
            }

            $last_id = $userModel->create($data); // The function that creates a new user returns the id of that user

            $this->successMessage("New user successfully added");
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
            $this->errorMessage("You cannot register a new user due to a processing error on the server");
            // echo $th->getMessage(); // view error
            return;
        }

        // Success login then redirect to login page
        return $this->view('login');
    }

    // handle login oauth by discord
    public function handleDiscordOauth($request)
    {
        try {
            $userModel = new UserModel();
            $discordOAuth = new DiscordOAuthModel();
            $code = $request["code"];
            $state = json_decode(base64_decode($request["state"]));
            $csrf = $_SESSION["csrf"];
            $_SESSION["csrf"] = "";

            $STATE = ["login", "register", "link_account"];

            if ($csrf !== $state->csrf) {
                $this->errorMessage("Error csrf");
                return;
            }

            if (!in_array($state->action, $STATE)) {
                $this->errorMessage("Invalid status");
                return;
            }

            if (!isset($code)) {
                $this->errorMessage("Invalid code");
                return;
            }

            $API_ENDPOINT = 'https://discord.com/api/v10';
            $CLIENT_ID = $_ENV["DISCORD_CLIENT_ID"];
            $CLIENT_SECRET = $_ENV["DISCORD_CLIENT_SECRET"];
            $REDIRECT_URI = $_ENV["DISCORD_REDIRECT_URI"];

            // handle login after accept from discord page
            // get access token
            $client = new \GuzzleHttp\Client();
            $verify_token = $client->request('POST', $API_ENDPOINT . "/oauth2/token", ["form_params" => [
                'client_id' => $CLIENT_ID,
                'client_secret' => $CLIENT_SECRET,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $REDIRECT_URI
            ]], [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);

            $token = json_decode($verify_token->getBody());

            $get_user = $client->request('GET', $API_ENDPOINT . "/oauth2/@me", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token->access_token,
                ]
            ]);
            $user = json_decode($get_user->getBody());

            // handle by action link account
            if ($state->action === "link_account") {
                $discord_find = $discordOAuth->first(['uid' => $user->user->id]);

                if (!$discord_find) {
                    $discordOAuth->create([
                        'expires_in' => $token->expires_in,
                        'access_token' => $token->access_token,
                        'refresh_token' => $token->refresh_token,
                        'uid' => $user->user->id,
                        'bind_id' => $_SESSION["user"]["id"],
                    ]);
                } else {
                    $userCheck = $userModel->first(["id" => $discord_find["bind_id"]]);

                    if ($userCheck["type_account"] === 1) {
                        $userModel->delete(["id" => $discord_find["bind_id"], 'type_account' => 1]);

                        $discordOAuth->update([
                            'expires_in' => $token->expires_in,
                            'access_token' => $token->access_token,
                            'refresh_token' => $token->refresh_token,
                            'bind_id' => $_SESSION["user"]["id"],
                        ], ['uid' => $user->user->id]);
                    } else {
                        $this->errorMessage("This discord account has been linked to another account before");
                        return $this->view("discord-account-link");
                    }
                }
            } else { // handle acction is register or login
                $discord_find = $discordOAuth->first(['uid' => $user->user->id]);
                if (!$discord_find) {
                    $ip = "";
                    $location = "";

                    try {
                        // get ip user
                        $client = new \GuzzleHttp\Client();
                        $response = $client->request('GET', 'https://ip-to-location1.p.rapidapi.com/myip?ip=' . $_SERVER['REMOTE_ADDR'], [
                            'headers' => [
                                'x-rapidapi-host' => 'ip-to-location1.p.rapidapi.com',
                                'x-rapidapi-key'     => 'f8df6ef829msh0c01c266522739cp12fac0jsn4c14924fd26e',
                                'Accept'     => 'application/json',
                            ]
                        ]);

                        $data = json_decode($response->getBody(), true);

                        $ip = $data["ip"];
                        $location = $data["geo"]["city"];
                    } catch (Exception $e) {
                    }

                    $data = [
                        'ip' => $ip,
                        'location' => $location,
                        'type_account' => 1
                    ];

                    $last_id = $userModel->create($data);

                    $discordOAuth->create([
                        'expires_in' => $token->expires_in,
                        'access_token' => $token->access_token,
                        'refresh_token' => $token->refresh_token,
                        'uid' => $user->user->id,
                        'bind_id' => $last_id,
                    ]);
                } else {
                    $discordOAuth->update([
                        'expires_in' => $token->expires_in,
                        'access_token' => $token->access_token,
                        'refresh_token' => $token->refresh_token
                    ], ['uid' => $user->user->id]);

                    $last_id = $discord_find["bind_id"];
                }

                $userLocal = $userModel->first(["id" => $last_id]);

                $_SESSION["user"] = $userLocal;
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
            $this->errorMessage("You cannot handle oauth discord due to a processing error on the server");
            // echo $th->getMessage(); // view error
            return;
        }
        if ($state->action === "login") {
            return $this->view("home");
        }
        if ($state->action === "register") {
            return $this->view("home");
        }

        return $this->view("discord-account-link");
    }

    // handle logout user
    public function logout()
    {
        unset($_SESSION["user"]);
        return $this->back();
    }

    // validate param login
    public function validateRequestLogin($data)
    {
        if (!isset($data['username']) || $data['username'] == "") {
            $this->errorMessage("Username is required");
            return false;
        }

        if (!isset($data['password']) || $data['password'] == "") {
            $this->errorMessage("Password is required");
            return false;
        }
        return true;
    }

    // validate param login oauth
    public function validateOAuth($data)
    {
        if (!isset($data['code']) || $data['code'] == "") {
            $this->errorMessage("Code is required");
            return false;
        }

        if (!isset($data['state']) || $data['state'] == "") {
            $this->errorMessage("State is required");
            return false;
        }
        return true;
    }

    // validate param register user
    public function validateRequestCreateUser($data)
    {
        if (!isset($data['username']) || $data['username'] == "") {
            $this->errorMessage("Username is required");
            return false;
        }
        if (!isset($data['password']) || $data['password'] == "") {
            $this->errorMessage("Password is required");
            return false;
        }
        if (!isset($data['re-password']) || $data['re-password'] == "") {
            $this->errorMessage("Re-Password is required");
            return false;
        }
        if ($data['password'] != $data['re-password']) {
            $this->errorMessage("Confirm passwords not match");
            return false;
        }
        return true;
    }
}

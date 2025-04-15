<?php
include_once "controller.php";
include_once "../model/userModel.php";
include_once "../model/discordOAuthModel.php";

class DiscordController extends Controller
{

    public function __construct() {}

    // get profile from database of discord
    function profile()
    {
        $discordOAuth = new DiscordOAuthModel();
        $profile = $discordOAuth->first(["bind_id" => $_SESSION["user"]["id"]]);

        if (!$profile) {
            return [];
        } else {
            return $profile;
        }
    }

    // get user from api
    function user()
    {
        $API_ENDPOINT = 'https://discord.com/api/v10';
        
        // handle get user from api bt token
        $client = new \GuzzleHttp\Client();
        $get_user = $client->request('GET', $API_ENDPOINT . "/oauth2/@me", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->profile()["access_token"],
            ]
        ]);
        $user = json_decode($get_user->getBody());

        return $user;
    }

    // get list guilds when user loged
    function guilds()
    {
        $API_ENDPOINT = 'https://discord.com/api/v10';

        // handle get list guilds when user loged
        $client = new \GuzzleHttp\Client();
        $get_guilds = $client->request('GET', $API_ENDPOINT . "/users/@me/guilds", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->profile()["access_token"],
            ]
        ]);
        $guilds = json_decode($get_guilds->getBody());

        return $guilds;
    }
}

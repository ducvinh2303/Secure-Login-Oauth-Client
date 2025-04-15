<?php
include_once "controller.php";
include_once "../model/logsModel.php";

class LogsController extends Controller
{

    public function __construct() {}

    // handle write log tom database
    public function writeLogs($state = "1") // 0 valid, 1 invalid
    {
        $logsModel = new LogsModel();

        $ip = $_SERVER['REMOTE_ADDR'];
        $path = $_SERVER["REQUEST_URI"];
        $user_id = isset($_SESSION["user"]["id"]) ? $_SESSION["user"]["id"] : null;
        $date = date("Y-m-d H:i:s");

        $logsModel->create([
            'ip' => $ip,
            'path' => $path,
            'date' => $date,
            'user_id' => $user_id,
            'state' => $state
        ]);
    }

    // handle get log from database
    public function getLog($request)
    {
        try {
            // Check if ip_search is posted then get the value of ip_search variable otherwise it is null
            $ipSearch = isset($request['ip_search']) ? $request['ip_search'] : null;
            // Initialize the LogsModel object
            $logsModel = new LogsModel();

            // if no ip is posted then get all logs otherwise get with condition where ip
            if (!$ipSearch) {
                $list = $logsModel->sql('SELECT * FROM logs');
            } else {
                $list = $logsModel->sql('SELECT * FROM logs WHERE ip = "' . $ipSearch . '"');
            }

            // Returns 1 original data type, 1 parsed data type to json for display
            return [
                "data" => $list,
                "jsonData" => json_encode($list)
            ];
        } catch (\Throwable $th) {
            return [];
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DiemND
 * Date: 08/09/20
 * Time: 3:36 PM
 */

namespace App\utils;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class Utils
{
    public static function sendEmail ($data) {
        $emailService = config('sa.email_service_url');
        $token = self::getRefreshToken();
        $emailData = [
            'name' => 'Megaads Investment'
        ];
        if (isset($data['content'])) {
            $emailData['content'] = $data['content'];
        }
        if (isset($data['to'])) {
            $emailData['to'] = $data['to'];
        }
        if (isset($data['name'])) {
            $emailData['name'] = $data['name'];
        }
        if (isset($data['subject'])) {
            $emailData['subject'] = $data['subject'];
        }
        if ($token) {
            $emailData['token'] = $token;
            self::sendRequest($emailService . '/api/send-mail', $emailData,  "POST");
        }
    }

    protected static function getRefreshToken()
    {
        $emailService = config('sa.email_service_url');
        $options = array(
            'email' => config('sa.email_service_user'),
            'password' => config('sa.email_service_password')
        );
        $result = self::sendRequest($emailService . '/auth/login', $options, "POST");
        if (isset($result->token)) {
            return $result->token;
        }
        return null;
    }

    public static function sendRequest($url, $data = [], $method = "GET", $headers=[])
    {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($channel, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($channel, CURLOPT_MAXREDIRS, 3);
        curl_setopt($channel, CURLOPT_POSTREDIR, 1);
        if ( !empty($headers) ) {
            curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($channel, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        }
        if (isset($data['timeout'])) {
            curl_setopt($channel, CURLOPT_TIMEOUT, $data['timeout']);
        } else {
            curl_setopt($channel, CURLOPT_TIMEOUT, 60);
        }

        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 60);
        $response = curl_exec($channel);
        $responseInJson = json_decode($response);
        return isset($responseInJson->result) ? $responseInJson->result : $responseInJson;
    }

    public static function triggerAsyncRequest($url, $params = [], $method = "get") {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_HEADER, false);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($channel, CURLOPT_POST, $method == "post" || $method == "POST" ? true : false );
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_NOSIGNAL, 1);
        curl_setopt($channel, CURLOPT_TIMEOUT_MS, 600);
        curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($channel, CURLOPT_FOLLOWLOCATION, false);
        curl_exec($channel);
        curl_close($channel);
    }

    public static function checkPermission($token, $resource) {
        $result = false;
        $user = DB::table('users')->where('api_token', $token)->first();
        $permission = DB::table('permission')->where('resource', $resource)->first();
        if ($user && $permission) {
            $role = DB::table('role')->where('code', $user->role)->first();
            $userPermission = DB::table('role_permission')->where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if ($userPermission && $userPermission->access == 'ACTIVE') {
                $result = true;
            }
        }
        return $result;
    }

}

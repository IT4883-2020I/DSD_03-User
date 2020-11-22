<?php
namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;

class BaseService extends Controller {

    const STATUS_SUCCESSFUL = "successful";
    const STATUS_FAIL = "fail";

    protected function getSlug($text, $allowUnder = false) {
        static $charMap = array(
            "à" => "a", "ả" => "a", "ã" => "a", "á" => "a", "ạ" => "a", "ă" => "a", "ằ" => "a", "ẳ" => "a", "ẵ" => "a", "ắ" => "a", "ặ" => "a", "â" => "a", "ầ" => "a", "ẩ" => "a", "ẫ" => "a", "ấ" => "a", "ậ" => "a",
            "đ" => "d",
            "è" => "e", "ẻ" => "e", "ẽ" => "e", "é" => "e", "ẹ" => "e", "ê" => "e", "ề" => "e", "ể" => "e", "ễ" => "e", "ế" => "e", "ệ" => "e",
            "ì" => "i", "ỉ" => "i", "ĩ" => "i", "í" => "i", "ị" => "i",
            "ò" => "o", "ỏ" => "o", "õ" => "o", "ó" => "o", "ọ" => "o", "ô" => "o", "ồ" => "o", "ổ" => "o", "ỗ" => "o", "ố" => "o", "ộ" => "o", "ơ" => "o", "ờ" => "o", "ở" => "o", "ỡ" => "o", "ớ" => "o", "ợ" => "o",
            "ù" => "u", "ủ" => "u", "ũ" => "u", "ú" => "u", "ụ" => "u", "ư" => "u", "ừ" => "u", "ử" => "u", "ữ" => "u", "ứ" => "u", "ự" => "u",
            "ỳ" => "y", "ỷ" => "y", "ỹ" => "y", "ý" => "y", "ỵ" => "y",
            "À" => "A", "Ả" => "A", "Ã" => "A", "Á" => "A", "Ạ" => "A", "Ă" => "A", "Ằ" => "A", "Ẳ" => "A", "Ẵ" => "A", "Ắ" => "A", "Ặ" => "A", "Â" => "A", "Ầ" => "A", "Ẩ" => "A", "Ẫ" => "A", "Ấ" => "A", "Ậ" => "A",
            "Đ" => "D",
            "È" => "E", "Ẻ" => "E", "Ẽ" => "E", "É" => "E", "Ẹ" => "E", "Ê" => "E", "Ề" => "E", "Ể" => "E", "Ễ" => "E", "Ế" => "E", "Ệ" => "E",
            "Ì" => "I", "Ỉ" => "I", "Ĩ" => "I", "Í" => "I", "Ị" => "I",
            "Ò" => "O", "Ỏ" => "O", "Õ" => "O", "Ó" => "O", "Ọ" => "O", "Ô" => "O", "Ồ" => "O", "Ổ" => "O", "Ỗ" => "O", "Ố" => "O", "Ộ" => "O", "Ơ" => "O", "Ờ" => "O", "Ở" => "O", "Ỡ" => "O", "Ớ" => "O", "Ợ" => "O",
            "Ù" => "U", "Ủ" => "U", "Ũ" => "U", "Ú" => "U", "Ụ" => "U", "Ư" => "U", "Ừ" => "U", "Ử" => "U", "Ữ" => "U", "Ứ" => "U", "Ự" => "U",
            "Ỳ" => "Y", "Ỷ" => "Y", "Ỹ" => "Y", "Ý" => "Y", "Ỵ" => "Y"
        );

        $text = strtr($text, $charMap);

        $text = $this->cleanUpSpecialChars($text, $allowUnder);
        return strtolower($text);
    }

    protected function cleanUpSpecialChars($text, $allowUnder = false) {
        $regExpression = "`\W`i";
        if ($allowUnder)
            $regExpression = "`[^a-zA-Z0-9-]`i";

        $text = preg_replace(array($regExpression, "`[-]+`",), "-", $text);
        return trim($text, "-");
    }

    protected function stringToDateOrNull($dateString) {
        if ($dateString == null || strlen($dateString) == 0) {
            return null;
        }
        $retVal = \DateTime::createFromFormat("d/m/Y", $dateString);
        $retVal->setTime(0, 0, 0);
        return $retVal;
	}
	
    /**
     * 
     * @param type $url
     * @param type $params :parameters as string. Example: id=1&pageId=0&pageSize=10
     * @param type $method
     */
    protected function triggerAsyncRequest($url, $params = "", $method = "get") {
        $channel = curl_init();
        if ($method == "get" || $method == "GET") {
            curl_setopt($channel, CURLOPT_URL, $url . "?" . $params);
        } else if ($method == "post" || $method == "POST") {
            curl_setopt($channel, CURLOPT_URL, $url);
            curl_setopt($channel, CURLOPT_POST, true);
            curl_setopt($channel, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($channel, CURLOPT_NOSIGNAL, 1);
        curl_setopt($channel, CURLOPT_TIMEOUT_MS, 200);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($channel);
        curl_close($channel);
    }

    protected function sendRequest($url, $params = [], $method = "GET") {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($channel, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $response = curl_exec($channel);
        curl_close($channel);
        $responseInJson = json_decode($response);
        return isset($responseInJson->result) ? $responseInJson->result : $response;
    }

    protected function buildFilter($request)
    {
        $filter = [
            'page_size' => 40,
            'page_id' => 1,
            'columns' => ['*']
        ];
        if ($request->has('page_size')) {
        $filter['page_size'] = $request->input('page_size');
    }
        if ($request->has('page_id') && $request->input('page_id') > 0) {
            $filter['page_id'] = $request->input('page_id');
        }
        if ($request->has('create_from') && $request->input('create_from')) {
            $createFrom = $this->stringToDateOrNull($request->input('create_from'));
            $filter['create_from'] = $createFrom;
        }
        if ($request->has('create_to') && $request->input('create_to')) {
            $createTo = $this->stringToDateOrNull($request->input('create_to'));
            $filter['create_to'] = $createTo;
        }
        return $filter;
    }

    protected function response($data)
    {
        if (!isset($data['status'])) {
            $data['status'] = self::STATUS_SUCCESSFUL;
        }
        return response()->json($data);
    }

    protected function success($result)
    {
        return $this->response([
            'status' => self::STATUS_SUCCESSFUL,
            'result' => $result,
        ]);
    }

    protected function fail($result)
    {
        return $this->response([
            'status' => self::STATUS_FAIL,
            'result' => $result,
        ]);
    }

    protected function recordsCountToPagesCount($recordsCount, $pageSize) {
        $retVal = (int) ($recordsCount / $pageSize);
        if ($recordsCount % $pageSize > 0) {
            $retVal++;
        }
        return $retVal;
    }

    protected function getDaysBetweenToDate ($fromDate, $toDate) {
        $toDate = strtotime($toDate);
        $fromDate = strtotime($fromDate);
        $datediff = $toDate - $fromDate;
        return round($datediff / (60 * 60 * 24));
    }


}

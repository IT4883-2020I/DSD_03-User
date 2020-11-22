<?php 
namespace App\Http\Controllers\Service;

use App\Models\Image;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;

class IncidentService extends BaseService {

    public function saveImageIncident(Request $request) {
        $response = $this->fail('');
        $data = $request->all();
        unset($data['api_token']);
        try {
            Image::insert($data);
            $response = $this->success('');
        } catch( Exception $ex ) {
            $response = $this->fail($ex->getMessage());
        }
        return $response;
    }

    public function saveVideoIncident(Request $request) {
        $response = $this->fail('');
        $data = $request->all();
        try {
            Video::insert($data);
            $response = $this->success('');
        } catch( Exception $ex ) {
            $response = $this->fail($ex->getMessage());
        }
        return $response;
    }
}
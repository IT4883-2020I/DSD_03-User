<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class Payload extends BaseModel
{
    protected $table = 'payload';

    protected $fillable = ['name', 'drone_id', 'status', 'type', 'start_time_use', 'manufacturer', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class DroneSchedule extends BaseModel
{
    protected $table = 'drone_schedule';

    protected $fillable = ['drone_id', 'user_schedule_id', 'path_id', 'created_at', 'updated_at'];
    
    protected $with = [];

    protected $append = [];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function drone() {
        return $this->belongsTo(Drone::class);
    }

    public function path() {
        return $this->belongsTo(Path::class);
    }
}

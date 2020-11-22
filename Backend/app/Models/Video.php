<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class Video extends BaseModel {
    protected $table = 'video';

    protected $fillable = ['name', 'url', 'drone_schedule_id', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function user() {
        return $this->belongsTo(User::class, 'actor_id', 'id');
    }

    public function dronesOrIncidents() {
        if ($this->type == 'INCIDENT') {
            return $this->belongsTo(Incident::class, 'target_id', 'id');
        } else {
            return $this->belongsTo(Drone::class, 'target_id', 'id');
        }
    }
}

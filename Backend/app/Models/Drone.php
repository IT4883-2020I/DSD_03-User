<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class Drone extends BaseModel
{
    
    protected $table = 'drone';

    protected $fillable = ['name', 'status', 'type', 'speed', 'manufacturer', 'user_id', 'created_at', 'updated_at'];

    public function user() {        
        return $this->belongsTo('App\Models\User');
    }

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function images() {
        return $this->hasMany(Image::class, 'target_id', 'id')->where('type', 'DRONE');
    }

    public function videos() {
        return $this->hasMany(Video::class, 'target_id', 'id')->where('type', 'DRONE');
    }
}

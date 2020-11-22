<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class UserSchedule extends BaseModel
{
    protected $table = 'user_schedule';

    protected $fillable = ['user_id', 'name', 'description', 'created_at', 'updated_at'];
    
    protected $with = [];

    protected $append = [];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

}

<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class Area extends BaseModel
{
    protected $table = 'area';
    
    protected $fillable = ['name', 'description', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

}

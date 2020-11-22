<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;

class Path extends BaseModel
{
    protected $table = 'path';

    protected $fillable = ['name', 'area_id', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function area() {
        return $this->belongsTo(Area::class);
    }
}

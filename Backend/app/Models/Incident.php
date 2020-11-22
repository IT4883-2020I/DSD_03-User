<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

class Incident extends BaseModel
{
    protected $table = 'incident';
    
    protected $fillable = ['name', 'type', 'description', 'status', 'image', 'video', 'user_id', 'created_at', 'updated_at'];

    protected $with = [];

    protected $append = [];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function user() {        
        return $this->belongsTo('App\Models\User');
    }

    public function images() {        
        return $this->hasMany(Image::class, 'target_id', 'id')->where('type', 'INCIDENT');
    }

    public function videos() {        
        return $this->hasMany(Video::class, 'target_id', 'id')->where('type', 'INCIDENT');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q']) && $_GET['q'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q'] . '%')
                    ->orwhere('description', 'like', '%' . $_GET['q'] . '%');
            });
        }
    }
}

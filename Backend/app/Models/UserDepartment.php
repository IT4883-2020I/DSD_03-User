<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

class UserDepartment extends BaseModel
{
    protected $table = 'user_n_department';

    protected $fillable = ['user_id', 'department_id', 'created_at', 'updated_at'];
    
    protected $with = ['user', 'department'];

    protected $append = [];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function user() {
        return $this->hasOne(User::class);
    }

    public function department() {
        return $this->hasOne(Department::class);
    }

}

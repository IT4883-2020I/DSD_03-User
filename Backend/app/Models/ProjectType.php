<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProjectType extends BaseModel
{
    protected $table = 'project_type';

    protected $fillable = ['name', 'description', 'code', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q_project']) && $_GET['q_project'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q_project'] . '%')
                    ->orWhere('description', 'like', '%' . $_GET['q_project'] . '%');
            });
        }
        //Check user role permission
        $token = null;
        if (array_key_exists("HTTP_TOKEN", $_SERVER)) {
            $token = $_SERVER['HTTP_TOKEN'];
        }
        $validUser = DB::table("users")
                            ->where('api_token', $token)
                            ->where('status', 'ACTIVE')
                            ->where("role", 'SUPER_ADMIN')
                            ->first();
        if (!$validUser) {
            throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
        }
        static::updating(function ($model) {
            throw new \Exception('Bạn không có quyền sửa!');
        });

        static::deleting(function ($model) {
            throw new \Exception('Bạn không có quyền xóa!');
        });
    }
}

<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProjectType extends BaseModel
{
    protected $table = 'user_log';

    protected $fillable = ['user_id', 'target_id', 'description', 'meta_data', 'type', 'ip_address', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q_log']) && $_GET['q_log'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q_log'] . '%')
                    ->orWhere('description', 'like', '%' . $_GET['q_log'] . '%')
                    ->orWhere('id', 'like', '%' . $_GET['q_log'] . '%');
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
                            ->first();
        if (!$validUser) {
            throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
        }

        //get project type
        $projectType = null;
        if (array_key_exists("HTTP_PROJECT_TYPE", $_SERVER)) {
            $projectType = $_SERVER['HTTP_PROJECT_TYPE'];
        }
        
        $systemPermission = Utils::checkPermission($token, 'UserLog.system');

        if (!$systemPermission) {
            static::addGlobalScope('getByType', function (Builder $builder) use ($projectType) {
                $builder->where('type', '=', $projectType);
            });
            if (!Utils::checkPermission($token, 'UserLog.find')) {
                throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
            }
        }

        static::updating(function ($model) {
            throw new \Exception('Bạn không có quyền sửa!');
        });

        static::deleting(function ($model) {
            throw new \Exception('Bạn không có quyền xóa!');
        });
    }
}

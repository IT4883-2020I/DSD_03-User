<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserMeta extends BaseModel
{
    protected $table = 'user_meta';
    
    protected $fillable = ['user_id', 'target_id', 'name', 'status', 'description', 'meta_data', 'type', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser() {
        return $this->belongsTo(User::class, 'target_id');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q_meta']) && $_GET['q_meta'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q_meta'] . '%')
                    ->orWhere('description', 'like', '%' . $_GET['q_meta'] . '%')
                    ->orWhere('id', 'like', '%' . $_GET['q_meta'] . '%');
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
        $systemPermission = Utils::checkPermission($token, 'UserMeta.system');

        if (!$systemPermission) {
            static::addGlobalScope('getByType', function (Builder $builder) use ($projectType) {
                $builder->where('type', '=', $projectType);
            });
            if (!Utils::checkPermission($token, 'UserMeta.find')) {
                throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
            }
        }

        static::creating(function ($model) use ($projectType, $validUser, $systemPermission) {
            $targetUser = DB::table("users")
                            ->where('id', $model->target_id)
                            ->where('status', 'ACTIVE')
                            ->first();
            if (!$targetUser || $targetUser->type !== $projectType) {
                throw new \Exception('Người dùng không thuộc dự án này!');
            }
            //Check user type permission
            if ($systemPermission) {
                if (!$projectType || $model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền của dự án này!');
                }
            }
        });

        static::updating(function ($model) use ($projectType, $validUser) {
            throw new \Exception('Bạn không có quyền sửa!');
        });

        static::deleting(function ($model) use ($projectType, $validUser) {
            //Check user type permission
            throw new \Exception('Bạn không có quyền xóa!');
        });
    }
}

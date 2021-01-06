<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Department extends BaseModel
{
    protected $table = 'department';
    
    protected $fillable = ['name', 'description', 'type', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q_department']) && $_GET['q_department'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q_department'] . '%')
                    ->orWhere('description', 'like', '%' . $_GET['q_department'] . '%')
                    ->orWhere('id', 'like', '%' . $_GET['q_department'] . '%');
            });
        }
        //Get project type
        $projectType = null;
        if (array_key_exists("HTTP_PROJECT_TYPE", $_SERVER)) {
            $projectType = $_SERVER['HTTP_PROJECT_TYPE'];
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

        $systemPermission = Utils::checkPermission($token, 'Department.system');
        
        if (!$validUser) {
            throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
        } else {
            if (!$systemPermission) {
                if (!$projectType || $validUser->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                static::addGlobalScope('getByType', function (Builder $builder) use ($projectType) {
                    $builder->where('type', '=', $projectType);
                });
                if (!Utils::checkPermission($token, 'Department.find')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
            }
        }

        static::creating(function ($model) use ($projectType, $validUser, $systemPermission) {
            if (!$systemPermission) {
                if (!Utils::checkPermission($validUser->api_token, 'Department.create')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                if ($model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền tạo phòng ban của dự án này!');
                }
            }
        });

        static::updating(function ($model) use ($projectType, $validUser, $systemPermission) {
            if (!$systemPermission) {
                if (!Utils::checkPermission($validUser->api_token, 'Department.update')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                if ($model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền sửa phòng ban của dự án này!');
                }
            }

        });

        static::deleting(function ($model) use ($projectType, $validUser, $systemPermission) {
            if (!$systemPermission) {
                if (!Utils::checkPermission($validUser->api_token, 'Department.delete')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                if (!$projectType || $model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền xóa phòng ban của dự án này!');
                }
            }
        });
    }
}

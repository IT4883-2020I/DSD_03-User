<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RolePermission extends BaseModel
{
    protected $table = 'role_permission';
    
    protected $fillable = ['role_id', 'permission_id', 'access', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permission() {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public static function boot() {
        parent::boot();
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

        $systemPermission = Utils::checkPermission($token, 'RolePermission.system');
        if (!$systemPermission || !$validUser) {
            throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
        }

        static::creating(function ($model) {
            throw new \Exception('Bạn không có quyền sửa!');
        });

        static::deleting(function ($model) {
            throw new \Exception('Bạn không có quyền xóa!');
        });
    }
}

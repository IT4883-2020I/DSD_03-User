<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Role extends BaseModel
{
    protected $table = 'role';
    
    protected $fillable = ['name', 'code', 'description', 'ranking', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q_role']) && $_GET['q_role'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q_role'] . '%')
                    ->orWhere('description', 'like', '%' . $_GET['q_role'] . '%');
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
        $systemPermission = Utils::checkPermission($token, 'Role.system');
        $findPermission = Utils::checkPermission($token, 'Role.find');

        if (!$systemPermission && !$findPermission) {
            throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
        }

        $userRole = DB::table('role')->where('code', $validUser->role)->first();
        $validRole = DB::table('role')->where('ranking', '>=', $userRole->ranking)->get()->toArray();
        $listLowerRole = [];
        foreach ($validRole as $key => $value) {
            $listLowerRole[$value->code] = $value->code;
        }
        static::addGlobalScope('getLowerRole', function (Builder $builder) use ($listLowerRole) {
            $builder->whereIn('code', $listLowerRole);
        });

        self::created(function ($model) {
            $permissions = DB::table('permission')->get();
            foreach ($permissions as $key => $value) {
                DB::table('role_permission')->insert([
                    'permission_id' => $value->id,
                    'role_id' => $model->id,
                    'access' => 'INACTIVE',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ]);
            }
        });
        self::deleted(function ($model) {
            DB::table('role_permission')->where('role_id', $model->id)->delete();
        });
        static::creating(function ($model) use ($systemPermission) {
            if (!$systemPermission) {
                throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
            }
        });
        static::updating(function ($model) use ($systemPermission) {
            if (!$systemPermission) {
                throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
            }
        });

        static::deleting(function ($model) use ($systemPermission) {
            if (!$systemPermission) {
                throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
            }
        });
    }
}

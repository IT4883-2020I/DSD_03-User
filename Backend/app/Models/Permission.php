<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Permission extends BaseModel
{
    protected $table = 'permission';
    
    protected $fillable = ['name', 'resource', 'description', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q_permission']) && $_GET['q_permission'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('name', 'like', '%' . $_GET['q_permission'] . '%')
                    ->orWhere('description', 'like', '%' . $_GET['q_permission'] . '%')
                    ->orWhere('resource', 'like', '%' . $_GET['q_permission'] . '%');
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

        $systemPermission = Utils::checkPermission($token, 'Permission.system');
        if (!$systemPermission || !$validUser) {
            throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
        }

        self::created(function ($model) {
            $roles = DB::table('role')->get();
            foreach ($roles as $key => $value) {
                $access = 'INACTIVE';
                if ($value->code == 'SUPER_ADMIN') {
                    $access = 'ACTIVE';
                }
                DB::table('role_permission')->insert([
                    'role_id' => $value->id,
                    'permission_id' => $model->id,
                    'access' => $access,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ]);
            }
        });

        self::deleted(function ($model) {
            DB::table('role_permission')->where('permission_id', $model->id)->delete();
        });
    }
}

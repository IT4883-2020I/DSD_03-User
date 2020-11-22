<?php

namespace App\Models;

use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class User extends BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const ROLE_MEMBER = 'MEMBER';
    const ROLE_ADMIN = 'ADMIN';

    const NICE_NAME = [
        'full_name' => 'Họ tên',
        'email' => 'Email',
        'type' => 'Dự án',
        'username' => "Tên đăng nhập",
        'password' => "Mật khẩu",
        'role' => "Chức vụ"
    ];

    const USER_VALIDATION_MESSAGES = [
        'username.regex' => 'Trường :attribute chỉ bao gồm các chữ cái, dấu cách và dấu -'
    ];

    const USER_CREATE_RULES = [
        'username' => 'bail|required|max:50|regex:/^[a-zA-Z \-àảãáạăằẳẵắặâầẩẫấậđèẻẽéẹêềểễếệìỉĩíịòỏõóọôồổỗốộơờởỡớợùủũúụưừửữứựỳỷỹýỵÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬĐÈẺẼÉẸÊỀỂỄẾỆÌỈĨÍỊÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢÙỦŨÚỤƯỪỬỮỨỰỲỶỸÝỴ]+$/',
        'full_name' => 'bail|required|max:255',
        'email' => 'bail|required|email',
        'password' => 'bail|required|max:200',
        'type' => 'bail|required',
        'role' => 'bail|required'
    ];
    const USER_UPDATE_RULES = [
        'username' => 'bail|required|max:50|regex:/^[a-zA-Z \-àảãáạăằẳẵắặâầẩẫấậđèẻẽéẹêềểễếệìỉĩíịòỏõóọôồổỗốộơờởỡớợùủũúụưừửữứựỳỷỹýỵÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬĐÈẺẼÉẸÊỀỂỄẾỆÌỈĨÍỊÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢÙỦŨÚỤƯỪỬỮỨỰỲỶỸÝỴ]+$/',
        'full_name' => 'bail|required|max:255',
        'email' => 'bail|required|email',
        'type' => 'bail|required',
        'role' => 'bail|required'
    ];

    protected $table = "users";

    protected $fillable = [
        'full_name', 'username', 'phone', 'email', 'address', 'brithday', 'password', 'avatar', 'type', 'role', 'status', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function drones() {
        return $this->hasMany(Drone::class);
    }

    public function incidents() {
        return $this->hasMany(Incident::class);
    }

    public function videos() {
        return $this->hasMany(Video::class, 'actor_id', 'id');
    }

    public function images() {
        return $this->hasMany(Image::class, 'actor_id', 'id');
    }

    public static function checkEmailExists($email) {
        return User::where('email', '=', $email)->exists();
    }
    public static function checkUsernameExists($username) {
        return User::where('username', '=', $username)->exists();
    }
    public static function checkPhoneExists($phone) {
        return User::where('phone', '=', $phone)->exists();
    }

    public static function boot() {
        parent::boot();
        if (isset($_GET['q']) && $_GET['q'] != '') {
            static::addGlobalScope('search', function (Builder $builder) {
                $builder->where('full_name', 'like', '%' . $_GET['q'] . '%')
                    ->orwhere('address', 'like', '%' . $_GET['q'] . '%')
                    ->orwhere('phone', 'like', '%' . $_GET['q'] . '%')
                    ->orwhere('email', 'like', '%' . $_GET['q'] . '%');
            });
        }
        $projectType = null;
        if (array_key_exists("HTTP_PROJECT_TYPE", $_SERVER)) {
            $projectType = $_SERVER['HTTP_PROJECT_TYPE'];
        }
        if (!$projectType) {
            static::addGlobalScope('searchEmpty', function (Builder $builder) {
                $builder->where('id', '=', -1);
            });
        } else {
            static::addGlobalScope('searchType', function (Builder $builder) use ($projectType) {
                $builder->where('type', '=', $projectType);
            });
        }
        static::creating(function ($model) {
            $projectType = null;
            if (array_key_exists("HTTP_PROJECT_TYPE", $_SERVER)) {
                $projectType = $_SERVER['HTTP_PROJECT_TYPE'];
            }
            if (!$projectType || $model->type !== $projectType) {
                throw new \Exception('Bạn không có quyền tạo người dùng của dự án này!');
            }
            $model->password = app('hash')->make($model->password);
            $model->avatar = !$model->avatar || $model->avatar == null || $model->avatar == '' ? "/media/users/blank.png" : $model->avatar;
            $arrayModel = $model->toArray();
            $validator = Validator::make($arrayModel, self::USER_CREATE_RULES, self::USER_VALIDATION_MESSAGES, self::NICE_NAME);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
        });

        static::updating(function ($model) {
            $arrayModel = $model->toArray();
            $projectType = null;
            if (array_key_exists("HTTP_PROJECT_TYPE", $_SERVER)) {
                $projectType = $_SERVER['HTTP_PROJECT_TYPE'];
            }
            if (!$projectType || $model->type !== $projectType) {
                throw new \Exception('Bạn không có quyền sửa người dùng của dự án này!');
            }
            $validator = Validator::make($arrayModel, self::USER_UPDATE_RULES, self::USER_VALIDATION_MESSAGES, self::NICE_NAME);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
        });
    }
}

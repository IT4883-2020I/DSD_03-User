<?php

namespace App\Models;

use App\utils\Utils;
use \Megaads\Apify\Models\BaseModel;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class User extends BaseModel
{
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const ROLE_MEMBER = 'MEMBER';
    const ROLE_ADMIN = 'ADMIN';
    const LOG_URL = "http://14.248.5.197:5012/api/user";

    const NICE_NAME = [
        'full_name' => 'Họ tên',
        'email' => 'Email',
        'type' => 'Dự án',
        'username' => "Tên đăng nhập",
        'password' => "Mật khẩu",
        'role' => "Chức vụ",
        'phone' => "Số điện thoại"
    ];

    const USER_VALIDATION_MESSAGES = [
        'username.regex' => 'Trường :attribute chỉ bao gồm các chữ cái, dấu cách và dấu -'
    ];

    const USER_CREATE_RULES = [
        'username' => 'bail|required|unique:users|max:50|regex:/^[a-zA-Z0-9 \-àảãáạăằẳẵắặâầẩẫấậđèẻẽéẹêềểễếệìỉĩíịòỏõóọôồổỗốộơờởỡớợùủũúụưừửữứựỳỷỹýỵÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬĐÈẺẼÉẸÊỀỂỄẾỆÌỈĨÍỊÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢÙỦŨÚỤƯỪỬỮỨỰỲỶỸÝỴ]+$/',
        'full_name' => 'bail|required|min:6|max:255',
        'email' => 'bail|required|unique:users|email',
        'password' => 'bail|required|min:5|max:200',
        'phone' => 'bail|unique:users|regex:/(0)[0-9]{9}/|digits_between:10,11',
        'type' => 'bail|required',
        'role' => 'bail|required'
    ];

    protected $table = "users";

    protected $fillable = [
        'full_name', 'username', 'phone', 'email', 'address', 'birthday', 'password', 'avatar', 'type', 'role', 'status', 'department_id', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $with = ['department'];

    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
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
                    ->orWhere('address', 'like', '%' . $_GET['q'] . '%')
                    ->orWhere('id', 'like', '%' . $_GET['q'] . '%')
                    ->orWhere('phone', 'like', '%' . $_GET['q'] . '%')
                    ->orWhere('email', 'like', '%' . $_GET['q'] . '%');
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

        $systemPermission = Utils::checkPermission($token, 'User.system');

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
                if (!Utils::checkPermission($token, 'User.find')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
            }
        }

        $userRole = DB::table('role')->where('code', $validUser->role)->first();
        $validRole = DB::table('role')->where('ranking', '>=', $userRole->ranking)->get()->toArray();
        $listLowerRole = [];
        foreach ($validRole as $key => $value) {
            $listLowerRole[$value->code] = $value->code;
        }
        static::addGlobalScope('getLowerRole', function (Builder $builder) use ($listLowerRole) {
            $builder->whereIn('role', $listLowerRole);
        });

        static::creating(function ($model) use ($projectType, $validUser, $listLowerRole, $systemPermission) {
            //Check user type permission
            if (!$systemPermission) {
                if (!Utils::checkPermission($validUser->api_token, 'User.create')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                if ($model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền tạo người dùng của dự án này!');
                }
            }

            if (!in_array($model->role, $listLowerRole)) {
                if ($model->role != null) {
                    throw new \Exception('Bạn không có quyền tạo người dùng có chức vự này!');
                }
            }

            $arrayModel = $model->toArray();
            $arrayModel['password'] = $model->password;
            $validator = Validator::make($arrayModel, self::USER_CREATE_RULES, self::USER_VALIDATION_MESSAGES, self::NICE_NAME);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $model->password = app('hash')->make($model->password);
            $model->avatar = !$model->avatar || $model->avatar == null || $model->avatar == '' ? "/media/users/blank.png" : $model->avatar;
            $dataLog = [
                'user_id' => $validUser->id,
                'target_id' => $model->id,
                'description' => "Thêm người dùng!",
                'meta_data' => json_encode($model),
                'project_type' => $model->type,
            ];
            Utils::triggerAsyncRequest(self::LOG_URL . "/add", $dataLog, "POST");
            unset($dataLog['project_type']);
            $dataLog['type'] = $model->type;
            $dataLog['created_at'] = new \DateTime();
            $dataLog['updated_at'] = new \DateTime();
            DB::table('user_log')->insert($dataLog);
        });

        static::updating(function ($model) use ($projectType, $validUser, $listLowerRole, $systemPermission) {
            //Check user type permission
            if (!$systemPermission) {
                if (!Utils::checkPermission($validUser->api_token, 'User.update')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                if ($model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền sửa người dùng của dự án này!');
                }
            }

            if (!in_array($model->role, $listLowerRole)) {
                throw new \Exception('Bạn không có quyền sửa người dùng có chức vự này!');
            }

            $arrayModel = $model->toArray();
            $id = $model->id;
            $email = $model->email;
            $username = $model->username;
            $updateRules = [
                'full_name' => 'bail|required|max:255',
                'email' => [
                    'bail',
                    'required',
                    'email',
                    Rule::unique('users')->where(function ($query) use($email, $id) {
                        return $query->where('email', $email)
                        ->where('id', '!=', $id);
                    }),
                ],
                'username' => [
                    'bail',
                    'required',
                    'regex:/^[a-zA-Z0-9 \-àảãáạăằẳẵắặâầẩẫấậđèẻẽéẹêềểễếệìỉĩíịòỏõóọôồổỗốộơờởỡớợùủũúụưừửữứựỳỷỹýỵÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬĐÈẺẼÉẸÊỀỂỄẾỆÌỈĨÍỊÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢÙỦŨÚỤƯỪỬỮỨỰỲỶỸÝỴ]+$/',
                    Rule::unique('users')->where(function ($query) use($username, $id) {
                        return $query->where('username', $username)
                        ->where('id', '!=', $id);
                    }),
                ],
                'type' => 'bail|required',
                'role' => 'bail|required'
            ];

            if ($model->phone && $model->phone != null && $model->phone != '') {
                $phone = $model->phone;
                $updateRules['phone'] = [
                    'bail',
                    'required',
                    'max:50',
                    'regex:/(0)[0-9]{9}/',
                    'digits_between:10,11',
                    Rule::unique('users')->where(function ($query) use($phone, $id) {
                        return $query->where('phone', $phone)
                        ->where('id', '!=', $id);
                    }),
                ];
            }

            $validator = Validator::make($arrayModel, $updateRules, self::USER_VALIDATION_MESSAGES, self::NICE_NAME);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            if ($model->password) {
                $model->password = app('hash')->make($model->password);
            }
            $dataLog = [
                'user_id' => $validUser->id,
                'target_id' => $model->id,
                'description' => "Sửa người dùng!",
                'meta_data' => json_encode($model),
                'project_type' => $model->type,
            ];
            Utils::triggerAsyncRequest(self::LOG_URL . "/edit", $dataLog, "POST");
            unset($dataLog['project_type']);
            $dataLog['type'] = $model->type;
            $dataLog['created_at'] = new \DateTime();
            $dataLog['updated_at'] = new \DateTime();
            $dataLog['ip_address'] = get_client_ip();
            DB::table('user_log')->insert($dataLog);
        });

        static::deleting(function ($model) use ($projectType, $validUser, $systemPermission) {
            //Check user type permission
            if (!$systemPermission) {
                if (!Utils::checkPermission($validUser->api_token, 'User.delete')) {
                    throw new \Exception('Bạn không có quyền sử dụng dịch vụ này!');
                }
                if (!$projectType || $model->type !== $projectType) {
                    throw new \Exception('Bạn không có quyền xóa người dùng của dự án này!');
                }
            }
            $dataLog = [
                'user_id' => $validUser->id,
                'target_id' => $model->id,
                'description' => "Xóa người dùng!",
                'meta_data' => json_encode($model),
                'project_type' => $model->type,
            ];
            Utils::triggerAsyncRequest(self::LOG_URL . "/delete", $dataLog, "POST");
            unset($dataLog['project_type']);
            $dataLog['type'] = $model->type;
            $dataLog['created_at'] = new \DateTime();
            $dataLog['updated_at'] = new \DateTime();
            $dataLog['ip_address'] = get_client_ip();
            DB::table('user_log')->insert($dataLog);
        });
    }
}

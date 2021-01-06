<?php

namespace App\Http\Controllers\Service;

use App\utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserService extends BaseService
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    const NICE_NAME = [
        'full_name' => 'Họ tên',
        'email' => 'Email',
        'type' => 'Dự án',
    ];

    const USER_VALIDATION_MESSAGES = [];

    const USER_REGISTER_RULE = [
        'full_name' => 'bail|required|max:255',
        'email' => 'bail|required|email',
        'password' => 'bail|required|max:200',
        'type' => 'bail|required',
    ];

    public function login(Request $request)
    {
        $result = [
            'status' => 'fail'
        ];
        $statusCode = 200;
        if ($request->has('username') && $request->has('password')) {
            $username = $request->input('username');
            $user = \DB::table('users')->where('phone', $username)
                ->orWhere('email', $username)
                ->orWhere('username', $username)
                ->first();
            if ($user && app('hash')->check($request->input('password'), $user->password)) {
                if ($user->status == 'ACTIVE') {
                    unset($user->password);
                    $result = [
                        'status' => 'successful',
                        'result' => $user
                    ];
                } else {
                    $statusCode = 403;
                    $result['message'] = 'Tài khoản của bạn chưa được kích hoạt!';
                }
            } else {
                $statusCode = 401;
                $result['message'] = 'Tài khoản hoặc mật khẩu không đúng!';
            }
        } else {
            $statusCode = 400;
            $result['message'] = 'Yêu cầu nhập tên đăng nhập và mật khẩu!';
        }
        return response()->json($result, $statusCode);
    }

    public function register(Request $request)
    {
        $result = [
            'status' => 'fail'
        ];
        $input = $request->all();
        $validator = Validator::make($input, self::USER_REGISTER_RULE, self::USER_VALIDATION_MESSAGES, self::NICE_NAME);

        if ($validator->fails()) {
            $result['message'] = $validator->errors()->first();
            return $result;
        }
        $statusCode = 200;
        if ($request->has('email') && $request->has('password')) {
            $userExists = \DB::table('users')->where('email', $request->input('email'))->exists();
            if (!$userExists) {
                $user['email'] = $request->input('email');
                $user['full_name'] = $request->input('full_name');
                $user['type'] = $request->input('type');
                $user['password'] = app('hash')->make($request->input('password'));
                $user['avatar'] = "/media/users/blank.png";
                $user['created_at'] = new \Datetime();
                $user['updated_at'] = new \Datetime();
                $newUserId = \DB::table('users')->insertGetId($user);
                $newUser = \DB::table('users')->where('id', $newUserId)->first();
                unset($newUser->password);
                $result = [
                    'status' => 'successful',
                    'result' => $newUser
                ];
            } else {
                $result['message'] = 'Email đã tồn tại';
            }
        } else {
            $result['message'] = 'Dữ liệu không đầy đủ';
        }
        return response()->json($result, $statusCode);
    }

    public function changePassword(Request $request)
    {
        $result = [
            'status' => 'fail'
        ];
        $statusCode = 200;
        if ($request->has('email') && $request->has('oldPassword')) {
            $user = \DB::table('users')->where('email', $request->input('email'))->first();

            if ($user && app('hash')->check($request->input('oldPassword'), $user->password)) {
                $user->password = app('hash')->make($request->input('password'));
                \DB::table('users')->where('email', $request->input('email'))->update(['password' => $user->password]);
                $result = [
                    'status' => 'successful',
                    'result' => $user
                ];
            } else {
                $statusCode = 401;
                $result = [
                    'status' => 'fail',
                    'message' => 'Mật khẩu cũ không đúng.',
                ];
            }
        } else {
            $statusCode = 400;
            $result['message'] = 'Dữ liệu không đầy đủ!';
        }
        return response()->json($result, $statusCode);
    }

    public function verifyToken(Request $request)
    {
        $response = [
            'status' => 'fail'
        ];
        $statusCode = 200;
        $token = $request->header('api-token');
        $projectType = $request->header('project-type');
        if ($token && $projectType) {
            $user = \DB::table('users')->where('api_token', $token)->where('type', $projectType)->where('status', 'ACTIVE')->first();
            unset($user->password);
            if ($user && $user->id) {
                $response = [
                    'status' => 'successful',
                    'result' => $user,
                ];
            } else {
                $statusCode = 401;
                $response['message'] = "User token hoặc loại dự án không đúng!";
            }
        } else {
            $statusCode = 400;
            $response['message'] = "Token và loại dự án là bắt buộc!";
        }
        return response()->json($response, $statusCode);
    }

    public function sendEmailForgotPassword(Request $request)
    {
        $response = [
            'status' => 'fail',
        ];
        try {
            $email = $request->input("email", "");
            if ($email != "") {
                $subject = "Quên mật khẩu";
                $user = \DB::table('users')->where('email', $email)->first();
                if ($user && $user->id) {
                    $userPassword = config('mail.user_recruitment');
                    $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
                        ->setUsername($userPassword['username'])
                        ->setPassword($userPassword['password']);
                    $mailer = new \Swift_Mailer($transport);
                    \Mail::setSwiftMailer($mailer);
                    $newPassword = $this->generateRandomString(12);
                    $receiver = $user->full_name;
                    \Mail::send('forgot-pasword-template', array(
                        "receiver" => $receiver,
                        "password" => $newPassword,
                    ), function ($message) use ($receiver, $email, $subject, $userPassword) {
                        $message->from($userPassword['username'], $userPassword['name']);
                        $message->to($email, $receiver)->subject($subject);
                    });
                    $user->password = app('hash')->make($newPassword);
                    \DB::table('users')->where('email', $email)->update(['password' => $user->password]);
                    $response = [
                        'status' => 'successful',
                    ];
                } else {
                    $response['message'] = "Người dùng không tồn tại. Vui lòng kiểm tra lại email";
                }
            }
        } catch (\Exception $ex) {
            $response['message'] = $ex->getMessage() . ' - LINE: ' . $ex->getLine();
        }
        return response()->json($response);
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function updateStatusActivation(Request $request)
    {
        $response = [
            'status' => 'fail',
        ];
        $statusCode = 200;
        $token = $request->header('token');
        $validUser = \DB::table("users")->where('api_token', $token)
            ->where('status', 'ACTIVE')
            ->whereIn("role", ['SUPER_ADMIN', 'ADMIN', 'MANAGER', 'SUPERVISOR'])
            ->first();
        if ($request->has('status_activation') && $request->has('user_id')) {
            $status = $request->input('status_activation');
            $id = $request->input('user_id');
            $userUpdate = \DB::table('users')->where('id', $id)->first();
            if ($userUpdate->type == $validUser->type) {
                \DB::table('users')->where('id', $id)->update(['status_activation' => $status]);
                $response = [
                    'status' => 'successful',
                    'message' => 'true',
                ];
            } else {
                $statusCode = 403;
                $response['message'] = "Bạn không có quyền sửa người dùng của dự án này!";
            }
        } else {
            $response['message'] = 'Vui lòng kiểm tra lại dữ liệu!';
            $statusCode = 400;
        }
        return response()->json($response, $statusCode);
    }

    public function generateDepartment()
    {
        $response = [
            'status' => 'successful',
        ];
        $users = \DB::table('users')->get();
        foreach ($users as $key => $value) {
            $listDepartment = \DB::table("department")->where('type', $value->type)->get()->toArray();
            if (count($listDepartment) > 0) {
                \DB::table('users')->where('id', $value->id)->update(['department_id' => $listDepartment[array_rand($listDepartment)]->id]);
            }
        }
        return response()->json($response);
    }

    public function generatePermission() {
        $roles = \DB::table('role')->get();
        $permissions = \DB::table('permission')->get();
        foreach ($roles as $index => $role) {
            foreach ($permissions as $key => $permission) {
                $access = 'INACTIVE';
                if ($role->code == 'SUPER_ADMIN') {
                    $access = 'ACTIVE';
                }
                \DB::table('role_permission')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'access' => $access,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ]);
            }
        }
    }

    public function getPermission(Request $request) {
        $response = [
            'status' => 'fail'
        ];
        $statusCode = 200;
        $token = $request->header('token');

        if ($token && $request->has('resource')) {
            $check = Utils::checkPermission($token, $request->input('resource'));
            if (!$check) {
                $response['message'] = 'Bạn không có quyền sự dụng dịch vụ này!';
                $statusCode = 403;
            } else {
                $response['result'] = true;
                $response['status'] = 'successful';
            }
        } else {
            $response['message'] = 'Vui lòng kiểm tra lại dữ liệu!';
            $statusCode = 400;
        }
        return response()->json($response, $statusCode);
    }

    public function getUsersName(Request $request) {
        $response = [
            'status' => 'fail',
        ];
        if ($request->has('ids')) {
            $listId = $request->input('ids');
            $users = \DB::table('users')->whereIn('id', $listId)->get(['id', 'full_name']);
            $response = [
                'status' => 'Successful',
                'result' => $users,
            ];
        }
        return response()->json($response);
    }
}
<?php

namespace Modules\Authentication\Repositories\WebService;

use Modules\Core\Packages\SMS\SmsGetWay;
use Modules\User\Entities\PasswordReset;
use Modules\User\Entities\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthenticationRepository
{
    protected $user;
    protected $password;
    protected $sms;

    function __construct(User $user, PasswordReset $password, SmsGetWay $sms)
    {
        $this->password = $password;
        $this->user = $user;
        $this->sms = $sms;
    }

    public function register($request)
    {
        DB::beginTransaction();
        $have_sms = config("app.have_sms");

        try {
            $user = $this->user->create([
                'name' => $request['name'],
                'email' => $request['email'] ?? null,
                'calling_code' => $request['calling_code'] ?? '965',
                'mobile' => $request['mobile'] ?? null,
                'country_id' => $request['country_id'] ?? null,
                'password' => Hash::make($request['password']),
                'image' => '/uploads/users/user.png',
                "is_verified" => !$have_sms,
                "firebase_uuid" => $request->firebase_uuid,
                "code_verified" => $have_sms ? generateRandomNumericCode() : null,
                "setting" => [
                    "lang" => locale()
                ],
            ]);

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }

    public function findUserByEmail($request)
    {
        $user = $this->user->where('email', $request->email)->first();
        return $user;
    }

    public function findUserByMultipleColumns($columns = [])
    {
        $query = $this->user->query();
        foreach ($columns as $key => $column) {
            $query = $query->where($key, $column);
        }
        return $query->first();
    }

    public function createToken($request)
    {
        $user = $this->findUserByEmail($request);

        $this->deleteTokens($user);

        $newToken = strtolower(str_random(64));

        $token = $this->password->insert([
            'email' => $user->email,
            'token' => $newToken,
            'created_at' => Carbon::now(),
        ]);

        $data = [
            'token' => $newToken,
            'user' => $user
        ];

        return $data;
    }

    public function resetPassword($request)
    {
        $user = $this->findUserByEmail($request);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        $this->deleteTokens($user);

        return true;
    }

    public function deleteTokens($user)
    {
        $this->password->where('email', $user->email)->delete();
    }

    public function changePasswordForMobile($request, $user)
    {
        if ($request['password'] == null)
            $password = $user['password'];
        else
            $password = \Illuminate\Support\Facades\Hash::make($request['password']);

        DB::beginTransaction();
        try {

            $user->update([
                'password' => $password,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function findUser($mobile, $phone_code)
    {
        return $this->user->where([
            'mobile' => $mobile,
            'calling_code' => $phone_code
        ])->first();
    }

    public function resendCode($user)
    {
        $user->update([
            "code_verified" => generateRandomNumericCode()
        ]);
        $this->sendSms($user);
        return true;
    }

    public function sendSms($user)
    {
        $result = $this->sms->send($user->code_verified, $user->getPhone());
        return $result["Result"] == "false";
    }

    public function resendCodeToGuestUser($request, $codeVerified)
    {
        $mobile = $request->calling_code . $request->mobile;
        $result = $this->sms->send($codeVerified, $mobile);
        return $result["Result"] == "false";
    }

}

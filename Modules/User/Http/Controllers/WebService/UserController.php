<?php

namespace Modules\User\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Modules\User\Transformers\WebService\UserResource;
use Modules\User\Http\Requests\WebService\UpdateProfileRequest;
use Modules\User\Repositories\WebService\UserRepository as User;
use Modules\User\Http\Requests\WebService\ChangePasswordRequest;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;

class UserController extends WebServiceController
{
    protected $user;

    function __construct(User $user)
    {
        $this->user = $user;
    }

    public function profile()
    {
        $user = $this->user->userProfile();
        return $this->response(new UserResource($user));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        /*if (!empty($request->mobile) && empty($request->firebase_id) && $request->mobile != auth('api')->user()->mobile) {
            return $this->error(__('authentication::api.register.validation.firebase_id.required'));
        }*/
        $this->user->update($request);
        $user = $this->user->userProfile();
        return $this->response(new UserResource($user));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->user->changePassword($request);
        $user = $this->user->findById(auth()->id());
        return $this->response(new UserResource($user));
    }

    public function getVerifidCode(Request $request)
    {
        $columns = [
            'calling_code' => $request->calling_code ?? '965',
            'mobile' => $request->mobile,
        ];
        $user = $this->user->findUserByMultipleColumns($columns);
        return $this->response(["code" => optional($user)->code_verified ?? ""]);
    }
}

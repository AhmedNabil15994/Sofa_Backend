<?php

namespace Modules\User\Repositories\WebService;

use Modules\User\Entities\Address;
use DB;

class AddressRepository
{

    function __construct(Address $address)
    {
        $this->address = $address;
    }

    public function getAllByUsrId()
    {
        $authUserId = auth('api')->user() ? auth('api')->user()->id : null;
        return $this->address->where('user_id', $authUserId)->with('state')->orderBy('id', 'DESC')->get();
    }

    public function findById($id)
    {
        $authUserId = auth('api')->user() ? auth('api')->user()->id : null;
        return $this->address->where('user_id', $authUserId)->with('state')->find($id);
    }

    public function findByIdWithoutAuth($id)
    {
        $address = $this->address->with('state')->find($id);
        return $address;
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            $authUserId = auth('api')->user() ? auth('api')->user()->id : null;

            $address = $this->address->create([
                'email' => $request['email'] ?? auth('api')->user()->email,
                'username' => $request['username'] ?? auth('api')->user()->name,
                'mobile' => $request['mobile'] ?? auth('api')->user()->mobile,
                'address' => $request['address'],
                'block' => $request['block'],
                'street' => $request['street'],
                'building' => $request['building'],
                'state_id' => $request['state'],
                'user_id' => $authUserId,
                'avenue' => $request['avenue'] ?? null,
                'floor' => $request['floor'] ?? null,
                'flat' => $request['flat'] ?? null,
                'automated_number' => $request['automated_number'] ?? null,
            ]);

            DB::commit();
            return $address;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($request, $address)
    {
        DB::beginTransaction();

        try {

            $address->update([
                'email' => $request['email'] ?? auth('api')->user()->email,
                'username' => $request['username'] ?? auth('api')->user()->name,
                'mobile' => $request['mobile'] ?? auth('api')->user()->mobile,
                'address' => $request['address'],
                'block' => $request['block'],
                'street' => $request['street'],
                'building' => $request['building'],
                'state_id' => $request['state'],
                'avenue' => $request['avenue'] ?? null,
                'floor' => $request['floor'] ?? null,
                'flat' => $request['flat'] ?? null,
                'automated_number' => $request['automated_number'] ?? null
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $model = $this->findById($id);
            $model->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

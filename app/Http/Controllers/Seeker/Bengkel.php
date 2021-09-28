<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\ServiceResource;
use App\Models\Services;
use App\Models\User;

class Bengkel extends Controller
{

    public function index(User $id)
    {
        $user = $id;
        return response()->json(
            [
                'status'    => ($user == null) ? false : true,
                'message'   => ($user == null) ? 'Bengkel tidak ditemukan' : 'Berhasil',
                'data'      => $user
            ],
        );
    }


    public function service($id, $service_id)
    {
        $service = Services::where('id', $service_id)->first();
        return response()->json(
            [
                'status'    => ($service == null) ? false : true,
                'message'   => ($service == null) ? 'Service tidak ditemukan' : 'Berhasil',
                'data'      => $service
            ],
        );
    }


    public function allServices($id)
    {
        return ServiceResource::collection(Services::where('user_id', $id)->paginate(1000));
    }
}

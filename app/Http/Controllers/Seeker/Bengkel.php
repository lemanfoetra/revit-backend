<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\ServiceResource;
use App\Models\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class Bengkel extends Controller
{

    public function index($id)
    {
        $user = DB::table('users')
            ->select([
                'users.*',
                'wishlists.id AS wishlist_id',
            ])
            ->where('users.id', $id)
            ->leftJoin('wishlists', function ($join) {
                $join->on('users.id', '=', 'wishlists.bengkel_id');
                $join->on('wishlists.user_id', '=', DB::raw(JWTAuth::user()->id));
            })->first();

        return response()->json(
            [
                'status'    => $user == null ? false : true,
                'message'   => $user == null  ? 'Bengkel tidak ditemukan' : 'Berhasil',
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

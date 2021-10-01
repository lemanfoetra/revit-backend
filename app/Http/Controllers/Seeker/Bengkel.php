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
                'users.id',
                'users.name',
                'users.latitude',
                'users.longitude',
                'users.hashmap_code',
                'users.full_address',
                'users.provinsi',
                'users.kabkot',
                'users.kecamatan',
                'users.created_at',
                'users.updated_at',
                'wishlists.created_at AS wishlist_at',
            ])
            ->where('users.id', $id)
            ->leftJoin('wishlists', function ($join) {
                $join->on('users.id', '=', 'wishlists.bengkel_id');
                $join->on('wishlists.user_id', '=', DB::raw(JWTAuth::user()->id));
            })->get();
            
        return response()->json(
            [
                'status'    => count($user) == 0 ? false : true,
                'message'   => count($user) == 0 ? 'Bengkel tidak ditemukan' : 'Berhasil',
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

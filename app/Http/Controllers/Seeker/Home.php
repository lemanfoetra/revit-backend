<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sk\Geohash\Geohash;

class Home extends Controller
{

    public function slider()
    {
    }


    public function bengkelTerdekat($latitude, $longitude)
    {
        $hashCode   = $this->createGeoCode($latitude, $longitude);
        $hashCode   = substr($hashCode, 0, 5);
        $geoArea    = $this->encodeGeoCode($hashCode);

        $users = DB::table('users')
            ->where('hashmap_code', 'like', $hashCode . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['North'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['East'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['South'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['West'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['NorthEast'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['SouthEast'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['SouthWest'] . '%')
            ->orWhere('hashmap_code', 'like', $geoArea['NorthWest'] . '%')
            ->get();

        return response()->json(
            [
                'success' => count($users) > 0 ? true : false,
                'message' => count($users) > 0 ? 'Get list bengkel berhasil' : 'Tidak ditemukan bengkel terdekat',
                'data'    => $users,
            ],
            200
        );
    }


    protected function createGeoCode($latitude, $longitude)
    {
        $g = new Geohash();
        return $g->encode($latitude, $longitude, 10);
    }


    protected function encodeGeoCode($code)
    {
        $g = new Geohash();
        $result = $g->getNeighbors($code);
        return $result;
    }
}

<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sk\Geohash\Geohash;

class CariBengkel extends Controller
{


    public function cariBengkelTerdekat($keyword, $latitude, $longitude, $limit = 10)
    {
        $hashCode   = $this->createGeoCode($latitude, $longitude);
        $hashCode   = substr($hashCode, 0, 5);
        $geoArea    = $this->encodeGeoCode($hashCode);

        $results = DB::table('users')
            ->where('name', 'like', "%" . $keyword . "%")
            ->whereRaw("(
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ? OR
                hashmap_code LIKE ?
            )", [
                $hashCode . "%",
                $geoArea['North'] . "%",
                $geoArea['East'] . "%",
                $geoArea['South'] . "%",
                $geoArea['West'] . "%",
                $geoArea['NorthEast'] . "%",
                $geoArea['SouthEast'] . "%",
                $geoArea['SouthWest'] . "%",
                $geoArea['NorthWest'] . "%"
            ])->paginate($limit);

        return response()->json(
            $results,
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

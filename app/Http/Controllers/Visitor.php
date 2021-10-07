<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seeker\VisitorRequest;
use App\Models\Visitors;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class Visitor extends Controller
{

    public function index()
    {
        $result = DB::table('visitors')
            ->select(DB::raw('COUNT(id) AS total'))
            ->where('user_id', JWTAuth::user()->id)
            ->first();
        return response()->json(
            [
                'success' => $result->total != null ? true : false,
                'message' => $result->total != null ? 'Berhasil' : 'Gagal',
                'data'    => $result,
            ],
            $result->total != null ? 200 : 401
        );
    }


    public function store(VisitorRequest $request)
    {
        $visitor = Visitors::create(
            [
                'user_id' => $request->bengkel_id,
                'user_id_visitor' => JWTAuth::user()->id,
            ]
        );
        return response()->json(
            [
                'success' => $visitor != null ? true : false,
                'message' => $visitor != null ? 'Berhasil disimpan' : 'Gagal disimpan',
                'data'    => $visitor,
            ],
            $visitor != null ? 200 : 401
        );
    }
}

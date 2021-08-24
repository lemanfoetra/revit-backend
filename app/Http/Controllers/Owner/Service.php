<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\AddServiceRequest;
use App\Http\Requests\Owner\UpdateServiceRequest;
use App\Http\Resources\Owner\ServiceResource;
use App\Models\Services;
use Tymon\JWTAuth\Facades\JWTAuth;

class Service extends Controller
{
    public function index()
    {
        $user = JWTAuth::user();
        return ServiceResource::collection(Services::where('user_id', $user->id)->paginate(10));
    }


    public function show($service_id)
    {
        $data = Services::where('id', $service_id)->get()->first();
        return response()->json(
            [
                'status'    => ($data == null) ? false : true,
                'message'   => ($data == null) ? 'service tidak ditemukan' : 'show service berhasil',
                'data'      => $data
            ],
            ($data == null) ? 400 : 200
        );
    }


    public function store(AddServiceRequest $request)
    {
        $user = JWTAuth::user();
        $service = Services::create(
            [
                'user_id'       => $user->id,
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'price'         => $request->get('price'),
                'discount'      => $request->get('discount'),
            ]
        );
        return response()->json([
            'success'   => true,
            'message'   => 'Service berhasil disimpan',
            'data'      => $service
        ], 200);
    }


    public function update(UpdateServiceRequest $request, Services $service)
    {
        $service->update($request->all());
        return response()->json([
            'success'   => true,
            'message'   => 'Service berhasil di update',
            'data'      => $service
        ], 200);
    }


    public function delete($service)
    {
        Services::where('id', $service)->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Service berhasil di hapus',
            'data'      => null
        ], 200);
    }
}

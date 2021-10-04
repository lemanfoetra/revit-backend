<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seeker\AddWishlist;
use App\Http\Resources\Seeker\WishlistResource;
use App\Models\Wishlist as ModelsWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class Wishlist extends Controller
{

    public function index()
    {
        $user = JWTAuth::user();
        return WishlistResource::collection(
            ModelsWishlist::where('wishlists.user_id', $user->id)
                ->join('users',  'users.id', '=', 'wishlists.bengkel_id')
                ->paginate(15)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddWishlist $request)
    {
        $wishlist = ModelsWishlist::create([
            'user_id'   => JWTAuth::user()->id,
            'bengkel_id' => $request->bengkel_id,
        ]);
        return response()->json(
            [
                'success'   => true,
                'message'   => 'Wishlist berhasil disimpan',
                'data'      => $wishlist
            ]
        );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ModelsWishlist::where('id', $id)->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Wishlist berhasil di hapus',
            'data'      => null
        ], 200);
    }
}

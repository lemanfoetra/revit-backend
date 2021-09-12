<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Sk\Geohash\Geohash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password', 'role');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'message' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'success'   => true,
            'message'   => "Login berhasil",
            'data'      => JWTAuth::user(),
            'token'     => $token,
        ], 200);
    }


    public function register(Request $request)
    {
        if ($this->uniqueUser($request->get('email'), $request->get('role')) != null) {
            return response()->json(['success' => false, 'message' => 'Email telah digunakan'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|string',
            'email'     => 'required|string|email|max:255|'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Register gagal',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'name'      => $request->get('name'),
            'email'     => $request->get('email'),
            'role'      => $request->get('role'),
            'password'  => Hash::make($request->get('password')),
        ]);

        $token  = JWTAuth::fromUser($user);
        return response()->json(
            [
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data'    => $user,
                'token'   => $token
            ],
            201
        );
    }


    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['success' => false, 'message' => 'token_expired'], $e->getMessage());
        } catch (TokenInvalidException $e) {

            return response()->json(['success' => false, 'message' => 'token_invalid'], $e->getMessage());
        } catch (JWTException $e) {

            return response()->json(['success' => false, 'message' => 'token_absent'], $e->getMessage());
        }
        return response()->json(['success' => true, 'message' => 'success', 'data' => $user]);
    }


    public function update(Request $request)
    {
        try {
            $user = JWTAuth::user();
            $user = User::where('id', $user->id)->first();
            $user->update($request->all());

            // create Geohash code jika terdapat latitude - longitude
            if ($request->latitude != null && $request->longitude != null) {
                $user->update([
                    'hashmap_code' => $this->makeGeohashCode($request->latitude, $request->longitude)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Update data user berhasil.',
                'data'    => $user,
            ], 200);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    protected function uniqueUser($email, $role)
    {
        $user = new User();
        return $user->select(['email'])
            ->where('email', $email)
            ->where('role', $role)
            ->first();
    }


    protected function makeGeohashCode($latitude, $longitude)
    {
        $g = new Geohash();
        return $g->encode($latitude, $longitude, 10);
    }
}

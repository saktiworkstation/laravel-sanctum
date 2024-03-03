<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registerUser(Request $request){
        $datauser = new User();
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'proses validasai gagal',
                'data' => $validator->errors(),
            ], 401);
        }

        $datauser->name = $request->name;
        $datauser->email = $request->email;
        $datauser->password = Hash::make($request->password);
        $datauser->save();

        return response()->json([
            'status' => true,
            'message' => 'berhasil memasukkan data'
        ], 200);
    }

    public function loginUser(Request $request){
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'proses login gagal',
                'data' => $validator->errors(),
            ], 401);
        }

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'status' => true,
                'message' => 'email dan password tidak sesuai'
            ], 401);
        }

        $datauser = User::where('email', $request->email)->first();
        return response()->json([
            'status' => true,
            'message' => 'proses login berhasil',
            'token' => $datauser->createToken('api-product', ['product-store'])->plainTextToken,
        ]);
    }
}

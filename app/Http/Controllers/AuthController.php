<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private JWTService $jWTService
    )
    {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:64',
            'password' => 'required|string|max:64'
        ]);
        $user = User::where('email', $request->email)->first();
        // checking user exist
        if(!$user) {
            throw new AuthenticationException('Akun belum didaftarkan');
        } else {
            // checking password match
            if(Hash::check($request->password, $user->password)) {
                // password matched, create token
                $token = $this->jWTService->createToken($user->id);
                return $this->sendData(['token' => $token]);
                return $token;
            } else {
                throw new AuthenticationException('Email atau Password Salah');
            }
        }
    }
     
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:64',
            'branch_id' => 'required|numeric|exists:branches,id',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'user_type' => User::USER_EMPLOYEE,
            'branch_id' => $request->input('branch_id'),
        ]);

        return $this->sendOk();
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return $this->sendData([
            'name' => $user->name,
            'branch_id' => $user->branch_id,
            'branch_name' => $user->branch->name,
            'type' => $user->user_type
        ]);
    }
}

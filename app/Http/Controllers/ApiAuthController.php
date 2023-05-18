<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     */
    public function register(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'address_line_1' => 'required',
            'address_line_2' => 'nullable|string',
            'city' => 'required',
            'parish' => 'required',
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Customer::create([
            'phone_number' => $request->input('phone_number'),
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'parish' => $request->input('parish'),
            'user_id' => $user->id,

        ]);

        event(new Registered($user));

        return response()->json([
            'message' => 'registration successful.'
        ], 201);
    }

    /**
     * Authenticate a user.
     *
     */
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string']
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['errors' => [
                'message' => 'email or password incorrect.'
            ]], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    /**
     * Revoke the authenticated user's token.
     *
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logged out successfully.'
        ]);
    }
}

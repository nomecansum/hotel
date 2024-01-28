<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use Laravel\Passport\Passport;

class APIAuthController extends Controller
{

    //Registro de usuario
    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    
     // Inicio de sesión y creación de token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'string|email|required_without:username',
            'username' => 'string|required_without:email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if(isset($request->username)){
            $request['email']=$request['username'];
        }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'result'=>'error',
                'error' => 'Unauthorized',
                'timestamp'=>Carbon::now(),
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addDays($user->token_expires);
            
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'result'=>'ok',
            'timestamp'=>Carbon::now(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'result'=>'ok',
            'timestamp'=>Carbon::now(),
            'message' => 'Successfully logged out'
        ]);
    }


    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    //Obtener el objeto User como jso
    public function gen_token($usuario)
    {
        $user=User::find($usuario);
        Passport::personalAccessTokensExpireIn(Carbon::now()->addDays($user->token_expires));
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        //$token->expires_at = Carbon::now()->addDays($user->token_expires);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'result'=>'ok',
            'timestamp'=>Carbon::now()
        ]);
    }

    

    

}

    

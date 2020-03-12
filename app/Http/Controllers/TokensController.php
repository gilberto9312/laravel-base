<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

use Validator;

use Tymon\JWTAuth\Facades\JWTAuth;

class TokensController extends Controller
{
    //
    public function login(Request $request, $user){
        //var_dump($request->header('Password'));die;
        $ValidationHeaders = ValidationHeadersContent($request);
        $ValidationPassword = ValidationHeadersPassword($request);
        //var_dump($ValidationPassword);die;
        if($ValidationHeaders === false){
            return response()->json([
                'success' => false,
                'code' => '1.1.1',
                'message' => 'Wrong validation',
                'errors' => 'Validation'
            ], 422);

        }
        if($ValidationPassword === false){
            return response()->json([
                'success' => false,
                'code' => '1.1.2',
                'message' => 'Wrong validation',
                'errors' => 'Validation'
            ], 422);

        }
        $password = $request->header('Password');
        $valueSesion =  [
            'email'=>$user,
            'password'=>$password
        ];
        

        $validator = Validator::make($valueSesion,[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => '1.2.1',
                'message' => 'Wrong validation',
                'errors' => 'Validation'
            ], 422);
        }

        $token = JWTAuth::attempt($valueSesion);

        if ($token) {

            return response()->json([
                'token' => $token,
                'user' => User::where('email', $valueSesion['email'])->get()->first()
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'code' => 2,
                'message' => 'Wrong credentials',
                'errors' => $validator->errors()], 401);
        }

    }

    public function refreshToken()
    {

        $token = JWTAuth::getToken();

        try {
            $token = JWTAuth::refresh($token);
            return response()->json(['success' => true, 'token' => $token], 200);
        } catch (TokenExpiredException $ex) {
            // We were unable to refresh the token, our user needs to login again
            return response()->json([
                'code' => 3, 'success' => false, 'message' => 'Need to login again, please (expired)!'
            ]);
        } catch (TokenBlacklistedException $ex) {
            // Blacklisted token
            return response()->json([
                'code' => 4, 'success' => false, 'message' => 'Need to login again, please (blacklisted)!'
            ], 422);
        }

    }

    public function logout()
    {
        //  $this->validate($request, ['token' => 'required']);
        $token = JWTAuth::getToken();

        try {
            $token = JWTAuth::invalidate($token);
            return response()->json([
                'code' => 5, 'success' => true, 'message' => "You have successfully logged out."
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'code' => 6, 'success' => false, 'message' => 'Failed to logout, please try again.'
            ], 422);
        }

    }
}

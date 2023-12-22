<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $credenciais = $request->all(['email', 'password']);

        $token = auth('api')->attempt($credenciais);
        if($token){
            return response()->json(['token' => $token], 200);
        }else{
            return response()->json(['erro' => 'Usuário ou senha incorretos!'], 403);
        }
    }


    public function logout(){
        auth('api')->logout();
        return response()->json(['msg' => 'Logout realizado com sucesso!'], 200);
    }


    public function refresh(){
        $token = auth('api')->refresh();
        return response()->json(['token' => $token], 200);
    }


    public function me(){
        dd(auth()->user());
    }
}

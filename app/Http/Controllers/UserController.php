<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    public function register(Request $request){
        // Recoger post
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role  = "ROLE_USER";
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
    
        if (!is_null($email) && !is_null($password) && !is_null($name)) {
            // Create user
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            /* Generate password encrypted */
            $secure_password = hash('sha256', $password);
            $user->password = $secure_password;

            // Check duplicated user
            $isset_user = User::where('email', '=', $email)->first();

            if ($isset_user == null) {
                // Register user
                $user->save();
                $data = [
                    'status' => 'success',
                    'code' => 400,
                    'message' => 'Usuario registrado exitosamente.'
                ];
            }else{
                // Don't register user
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Usuario ya existente.'
                ];
            }

        }else{
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Usuario no creado.'
            ];
        }

        return response()->json($data, 200);

    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();

        // Receive POST data
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;
    
        // Cifrar password
        $secure_password = hash('sha256', $password);

        if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) {
            $signup = $jwtAuth->signup($email, $secure_password);

        }elseif ($getToken != null) {
            $signup = $jwtAuth->signup($email, $secure_password, $getToken);
        }else{
            $signup =[
                'status' => 'error',
                'message' => 'Envía tus datos por POST'
            ];
        }

        return response()->json($signup, 200);

    }
}

<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{

    public $key;
    public function __construct(){
        $this->key = 'secret-key-123456789@';
    }

    public function signup($email, $password, $getToken = null){
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }

        if ($signup) {
            // Generate token and return it
            $token = [
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            
            if (is_null($getToken)) {
                return $jwt;
            }else{
                return $decoded;
            }

        }else{
            // Return error
            return [
                'status' => 'error',
                'message' => 'Login ha fallado.'
            ];
        }

    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;

        /* verify if token is valid */

        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        }else{
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }
}

?>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Models\Car;
use App\Models\User;

class CarController extends Controller
{
    public function index(Request $request){
        $cars = Car::all()->load('user');
        return response()->json([
            'cars' => $cars,
            'status' => 'success'
        ], 200);
    }

    /* Metodos CRUD */
    public function store(Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Get user indentifiyed
            $user = $jwtAuth->checkToken($hash, true); // Devuelve el objeto usuario identificado

            // Get data from POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            
            
            // Validation API
            
            $validate = \Validator::make($params_array, [
                'title' => 'required | min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }
            

            // Store car
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->status = $params->status;
            $car->price = $params->price;

            $car->save();

            $data = [
                'car' => $car,
                'status' => 'success',
                'code' => 200
            ];
        }else{
            // Return error
            $data = [
                'message' => 'Login incorrecto.',
                'status' => 'error',
                'code' => 400
            ];
        }

        return response()->json($data, 200);
    }

    public function show($id){
        $car = Car::find($id)->load('user');
        return response()->json([
            'car' => $car,
            'status' => 'success'
        ], 200);
    }

    public function update($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Get data from POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            //Data validation
            $validate = \Validator::make($params_array, [
                'title' => 'required | min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }

            //Update car
            $car = Car::where('id', $id)->update($params_array);
            $data = [
                'car' => $params,
                'status' => 'success',
                'code' => 200
            ];


        }else{
            // Return error
            $data = [
                'message' => 'Login incorrecto.',
                'status' => 'error',
                'code' => 400
            ];
        }
    }

    public function destroy($id, Request $request){
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Check if register exist
            $car = new Car::find($id);

            //Delete register
            $car->delete();

            // Return deleted register
            $data = [
                'car' => $car,
                'status' => 'success',
                'code' => 200
            ];
        }else{
            $data = [
                'status' => 'error',
                'message' => 'Login Incorrecto.',
                'code' => 400
            ];
        }

        return response()->json($data, 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ApiToken;
use App\Models\User;

class ApiController extends Controller
{
    public function get_token()
    {
        $token = ApiToken::where('id', 1)->value('key');

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

    public function get_user_by_id($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|min:1|exists:users',
        ]);

        if($validator->fails()){

            $errors = $validator->errors();

            return response()->json([
                "success" => false,
                "message" => "Validation failed",
                "fails" => $errors,
            ]);
        }

        $user =  User::where('id', $id)->first();

        return response()->json([
            "success" => true,
            "user" => $user,
        ]);

    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ApiToken;
use App\Models\User;
use App\Models\Position;

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

    public function positions()
    {
        $positions = Position::get(['id', 'title']);

        return response()->json([
            'success' => true,
            'positions' => $positions,
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

    public function get_users(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'count' => [
                'required',
                'integer',
            ],
            'page' => [
                'required',
                'integer',
            ],
            'offset' => [
                'integer'
            ],
        ]);

        if($validator->fails()){

            $errors = $validator->errors();

            return response()->json([
                "success" => false,
                "message" => "Validation failed",
                "fails" => $errors,
            ]);
        }

        $count = $request->count;
        $offset = $request->offset;

        if($offset !== null){
            $users = User::offset($offset)->limit($count)->whereNot('id', 1)->orderBy('created_at', 'desc')->get();

            $response = response()->json([
                "success" => true,
                'count' => $count,
                "users" => $users,
            ]);

        }else{
            $users = User::orderBy('created_at', 'desc')->whereNot('id', 1)->paginate($count);

            $nextUrl = $users->nextPageUrl();
            if ($nextUrl) {
                $nextUrl .= '&count=' . $count;
            }
            $prevUrl = $users->previousPageUrl();
            if ($prevUrl) {
                $prevUrl .= '&count=' . $count;
            }

            $response = response()->json([
                "success" => true,
                'page' => $users->currentPage(),
                "total_pages" => $users->lastPage(),
                'total_users' => $users->total(),
                'count' => $users->count(),
                'links' => [
                    'next_url' => $nextUrl,
                    'prev_url' => $prevUrl,
                ],
                "users" => $users->items(),
            ]);
        }

        return $response;
    }
}

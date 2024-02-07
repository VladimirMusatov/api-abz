<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiToken;

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
}

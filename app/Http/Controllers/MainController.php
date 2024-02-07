<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class MainController extends Controller
{
    public function index()
    {
        $ApiController = new ApiController();

        $get_token = $ApiController->get_token();

        $token = $get_token->original['token'];

        return view('welcome', ['token' => $token]);
    }
}

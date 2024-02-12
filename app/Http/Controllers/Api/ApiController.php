<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\ApiToken;
use App\Models\User;
use App\Models\Position;
use App\Events\TokenRefreshed;

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

    public function create_user(Request $request)
    {

        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'The token expired.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:2',
                'max:60',
            ], 
            'email' => [
                'required',
                'unique:users',
                'email',
            ], 
            'position_id' => [
                'required',
                'integer', 
                'exists:positions,id',
            ],
            'phone' => [
                'required',
                'regex:/^380\d{9}$/',
            ],
            'photo' => [
                'required',
                'image', 
                'mimes:jpeg,jpg', 
                'max:5120', 
                'dimensions:min_width=70,min_height=70',
            ]

        ]);

        if($validator->fails()){

            $errors = $validator->errors();

            return response()->json([
                "success" => false,
                "message" => "Validation failed",
                "fails" => $errors,
            ]);
        }

        $photo = $request->photo;
        $file_name = uniqid(). '_' . $photo->getClientOriginalName();

        $filePath = Storage::disk('public')->putFileAs('uploads', $photo, $file_name);

        $imagePath = 'storage/' . $filePath;

        $url = asset($imagePath);

        // Compressed image
        $responseCompressImage = $this->compressImage($imagePath);
        if(!$responseCompressImage['success'])
        {
            response()->json([
                "success" => false,
                "message" => $responseCompressImage['error'],
            ]);
        }   

        $image = Image::make('storage/' . $filePath);

        unlink($imagePath);

        $image->fit(70, 70, function ($constraint) {
            $constraint->upsize();
        });

        $image->save(public_path('storage/' . $filePath));

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position_id' => $request->position_id,
            'phone' => '+' . $request->phone,
            'photo' => $url,
        ]);

        
        event(new TokenRefreshed());

        return response()->json([
            "success" => true,
            "message" => 'New user successfully registered',
        ]);

    }

    public function compressImage($imagePath)
    {

        $url = 'https://api.tinify.com/shrink';
        $api_key = env('TINYPNG_API_KEY');

        $fileContent = file_get_contents($imagePath);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode($api_key),
        ]);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        if ($info['http_code'] === 201) {
            $compressedData = json_decode($response, true);

            $compressedUrl = $compressedData['output']['url'];

            
            // Download Compressed Image
            $responseDownloadImg = $this->downloadCompressedImage($compressedUrl, $imagePath);

            if(!$responseDownloadImg['success'])
            {
                return [
                    'success' => false,
                    'error' => $responseDownloadImg['error'],
                ];
                
            }

            return [
                'success' => true,
                'message' => 'Image successfully compressed',
            ];
        } else {
            return [
                'success' => false,
                'error' => $response,
            ];
        }
    }

    public function downloadCompressedImage($compressedUrl, $imagePath)
    {

        $ch = curl_init($compressedUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $compressedImage = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        if ($info['http_code'] === 200) {
            file_put_contents($imagePath, $compressedImage);
    
            return [
                'success' => true,
                'message' => 'Compressed image downloaded successfully.',
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to download compressed image. HTTP code: ' . $info['http_code'],
            ];
        }
    }
}

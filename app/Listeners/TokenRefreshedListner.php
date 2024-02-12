<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\Events\TokenRefreshed;
use Illuminate\Support\Str;
use App\Models\ApiToken;
use App\Models\User;

class TokenRefreshedListner
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TokenRefreshed $event): void
    {
            $user = User::where('id', 1)->first();

            $user->tokens()->delete();
    
            $randomString  = Str::random(12);
    
            $token = $user->createToken($randomString)->plainTextToken;
    
            ApiToken::where('user_id', 1)->update(['key' => $token]);
    
    }
}
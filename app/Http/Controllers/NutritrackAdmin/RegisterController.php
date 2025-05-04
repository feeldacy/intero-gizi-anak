<?php

namespace App\Http\Controllers\NutritrackAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function __invoke(StoreUserRequest $request)
    {
        $registerUserData = $request->validated();

        $user = $this->userService->storeUserData($registerUserData);

        $user->assignRole('nutritrackAdmin');

        return response()->json([
            'message' => 'Admin Nutritrack Created ',
            'status' => 'Success'
        ], 200);
    }
}

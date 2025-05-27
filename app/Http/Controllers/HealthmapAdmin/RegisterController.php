<?php

namespace App\Http\Controllers\HealthmapAdmin;

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
        try {
            $registerUserData = $request->validated();
            $registerUserData['unit_id'] = 'DINKES';

            $user = $this->userService->storeHealthmapAdminData($registerUserData);

            $user->assignRole('healthmapAdmin');

            $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
            return response()->json([
                'message' => 'Admin Healthmap Created',
                'status' => 'Success',
                'access_token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Healthmap Admin',
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

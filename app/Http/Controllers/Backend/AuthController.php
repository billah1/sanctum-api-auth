<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestAuth;
use App\Http\Requests\RequestRegister;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function checkAuth(): JsonResponse
    {
        try {
            $user = '';
            if (auth()->check()) {
                $user = User::with('roles:user_id,name')->where('id', auth()->user()->id)->first();
                return sendSuccessResponse('User Authenticate', 200, $user);
            } else {
                return sendErrorResponse('User Unauthenticated', 404);
            }
        } catch (Exception $exception) {
            return sendErrorResponse('Something went wrong : ' . $exception->getMessage(), 404);
        }
    }

    public function login(RequestAuth $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            $token  = $user->createToken('MyAppToken')->plainTextToken;
            $data = [
                'user' => $user,
                'token' => $token
            ];
            return sendSuccessResponse('Logged in Successfully!!', '200', $data);
        }catch (Exception $exception) {
            return sendErrorResponse('Something went wrong: '. $exception->getMessage());
        }
    }


    public function register(RequestRegister $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $userType = $request->userType === 'trial' ? UserType::USER_TYPE_TRIAL : UserType::USER_TYPE_PAID;

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt('12345678')
            ]);
            $user->roles()->create([
                'name' => UserRole::ROLE_USER,
            ]);
            $user->type()->create([
                'title'         => $userType,
                'start_date'    => now(),
                'end_date'      => now()->addDays(7),
            ]);
            $user->userInformation()->create([
                'business_name' => $request->businessName,
                'business_type' => $request->businessType,
                'phone_number'  => $request->phoneNumber,
                'country'       => $request->country,
                'address'       => $request->address
            ]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return sendErrorResponse('Something went wrong : '.$exception->getMessage());
        }
        return sendSuccessResponse('User created successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            auth()->guard('web')->logout();

        }catch (Exception $exception) {
            return sendErrorResponse('Something went wrong: '. $exception->getMessage());
        }
        return sendSuccessResponse('Logout successful');

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth};

/**
 * @OA\Info(title="Blog API", version="1.0.0")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/register",
     *   tags={"Auth"},
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"name","email","password"},
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="password", type="string", format="password"),
     *   )),
     *   @OA\Response(response=201, description="Registered")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6'
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token'=>$token], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/login",
     *   tags={"Auth"},
     *   summary="Авторизация пользователя",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", example="user@example.com"),
     *       @OA\Property(property="password", type="string", example="secret123")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Успешная авторизация",
     *     @OA\JsonContent(
     *       @OA\Property(property="token", type="string", example="1|f1j0D8H7gVxW9abc...")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Неверные учетные данные",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Invalid credentials")
     *     )
     *   )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        $user = User::where('email',$credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'],$user->password)) {
            return response()->json(['message'=>'Invalid credentials'], 401);
        }
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token'=>$token]);
    }

    /**
     * @OA\Post(
     *   path="/api/logout",
     *   tags={"Auth"},
     *   summary="Выход пользователя из системы",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Успешный выход",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Logged out")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Неавторизован (нет токена)"
     *   )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

}

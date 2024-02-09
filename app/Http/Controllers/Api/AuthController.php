<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de Lead
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Realizar logueo"},
     *     summary="Realizar Inicio de Sesión para obtener Token",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="segundoNombre",
     *                      type="string"
     *                  ),
     *                 example={"email" : "api.pruebas@pompeyo.cl", "password" : "password", "token_name" :  "marca"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de Sesión"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso no encontrado."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt( [ 'email' => $credentials['email'], 'password' => $credentials['password'], 'state' => 1])){
            $token = $request->user()->createToken($request->token_name);

            return ['token' => $token->plainTextToken];
        }

        return response()->json("Usuario y/o contraseña inválido");

    }

    public function logout()
    {
        $user = auth()->user();
        foreach ($user->tokens as $token) {
            $token->delete();
        }
        return response()->json('Sessión terminada', 200);

    }
}

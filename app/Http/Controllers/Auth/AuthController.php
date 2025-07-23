<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\UseCases\Auth\LoginUserUseCase;
use App\UseCases\Auth\LogoutUserUseCase;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $loginUseCase;
    protected $logoutUseCase;

    public function __construct(LoginUserUseCase $loginUseCase, LogoutUserUseCase $logoutUseCase)
    {
        $this->loginUseCase = $loginUseCase;
        $this->logoutUseCase = $logoutUseCase;
    }

    public function login(Request $request)
    {
        return $this->loginUseCase->execute($request);
    }

    public function logout(Request $request)
    {
        return $this->logoutUseCase->execute($request);
    }
}

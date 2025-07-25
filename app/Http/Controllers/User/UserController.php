<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\UseCases\User\UserUseCase;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $useCase;

    public function __construct(UserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function index()
    {
        return response()->json($this->useCase->getAll());
    }

    public function show($id)
    {
        return response()->json($this->useCase->getById($id));
    }

    public function store(StoreUserRequest $request)
    {
        return response()->json($this->useCase->create($request->validated()), 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        return response()->json($this->useCase->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->useCase->delete($id);
        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}

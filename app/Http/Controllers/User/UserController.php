<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\UseCases\User\UserUseCase;
use Illuminate\Http\Request;
use App\Traits\PaginateTrait;

class UserController extends Controller
{
    use PaginateTrait;

    protected $useCase;

    public function __construct(UserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $users = $this->useCase->getAll($perPage, $page);

        $response = $this->paginate(
            $users['total'],
            $perPage,
            $page,
            $users['data']
        );

        return response()->json($response);
    }

    public function show($id)
    {
        $user = $this->useCase->find($id);
        return response()->json($user);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->useCase->create($request->validated());
        return response()->json($user, 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->useCase->update($id, $request->validated());
        return response()->json($user);
    }

    public function destroy($id)
    {
        $this->useCase->delete($id);
        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}

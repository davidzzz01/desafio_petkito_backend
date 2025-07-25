<?php

namespace App\UseCases\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserUseCase
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repository->create($data);
    }

    public function getAll($perPage, $page)
    {
        $offset = ($page - 1) * $perPage;

        $total = $this->repository->count();
        $users = $this->repository->all();

        return [
            'total' => $total,
            'per_page' => $perPage,
            'page' => $page,
            'data' => $users,
        ];
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function update($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}

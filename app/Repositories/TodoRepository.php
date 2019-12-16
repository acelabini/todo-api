<?php

namespace App\Repositories;

use App\Models\Todo;
use App\Models\User;

class TodoRepository extends Repository
{
    public function setModel()
    {
        $this->model = new Todo();
    }

    public function getAllByUserId(User $user)
    {
        return $this->model->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

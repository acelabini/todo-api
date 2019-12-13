<?php

namespace App\Repositories;

use App\Models\Todo;

class TodoRepository extends Repository
{
    public function setModel()
    {
        $this->model = new Todo();
    }
}

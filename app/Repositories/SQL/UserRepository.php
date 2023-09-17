<?php

namespace App\Repositories\SQL;

use App\Models\User;
use App\Repositories\Contracts\UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class UserRepository implements UserContract
{
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     *
     * **/
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     *
     * /**
     * @return Model
     */
    public function create($attributes): Model
    {
            return $this->model->create($attributes);
    }
}

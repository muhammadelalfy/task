<?php
namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface UserContract
{
    public function create(array $attributes): Model;
}

<?php

namespace App\Enums;

enum UserTypes: int
{
    const Admin = 1;
    const Supervisor = 2;
    const Arbitrator = 3;
}

<?php

namespace App\Enums;

enum RolesEnum: string //Remember to add types since its typescript.
{
    //This file is for setting all the permissions.
    case Admin = 'Admin';
    case User = 'User';
    case Vendor = 'Vendor';
}

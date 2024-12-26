<?php

namespace App\Enums;

enum RolesEnum: string //Remember to add types since its typescript.
{
    case Admin = 'Admin';
    case User = 'User';
    case Vendor = 'Vendor';
}

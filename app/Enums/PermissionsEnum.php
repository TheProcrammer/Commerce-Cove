<?php

namespace App\Enums;

enum PermissionsEnum:string
{
    //This file is for setting all the permissions.
    case ApproveVendors = 'ApprovedVendors';
    case SellProducts = 'SellProducts';
    case BuyProducts = 'BuyProducts';
}

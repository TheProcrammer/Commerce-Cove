<?php

namespace App\Enums;

enum PermissionsEnum:string
{
    case ApproveVendors = 'ApprovedVendors';
    case SellProducts = 'SellProducts';
    case BuyProducts = 'BuyProducts';
}

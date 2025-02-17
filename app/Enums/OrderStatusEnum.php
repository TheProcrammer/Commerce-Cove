<?php

namespace App\Enums;

enum OrderStatusEnum:string
{
    // Represents different statuses an order can have (Draft, Paid, Shipped, Delivered, Cancelled). 
    case Draft = 'draft';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public static function labels()
    {
        // Provides translated labels for these statuses, making it easier to display them in user interfaces.
        return [
            self::Draft->value => __('Draft'),
            self::Paid->value => __('Paid'),
            self::Shipped->value => __('Shipped'),
            self::Delivered->value => __('Delivered'),
            self::Cancelled->value => __('Cancelled'),
        ];
    }
}

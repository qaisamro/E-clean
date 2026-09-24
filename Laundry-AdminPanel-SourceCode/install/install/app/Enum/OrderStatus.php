<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'Pending';
    case CONFIRM = 'Order confirmed';
    case PICKED_UP = 'Picked up';
    case PROCESSING = 'Processing';
    case ON_GOING = 'On Going';
    case DELIVERED = 'Delivered';
    case CANCELLED = 'Cancelled';
}

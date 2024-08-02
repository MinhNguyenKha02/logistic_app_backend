<?php

namespace App\Enums;

enum Status:string {
    case Pending = 'pending';
    case Confirming = 'confirming';
    case Delivered = 'delivered';
    case Shipped = 'shipped';
    case Supplied = 'supplied';
    case Cancelled = 'cancelled';

    case Delivering = 'delivering';

    case Shipping = 'shipping';

    case Supplying = 'supplying';

}

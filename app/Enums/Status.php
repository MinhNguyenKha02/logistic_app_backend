<?php

namespace App\Enums;

enum Status:string {

    case Breakdown = 'breakdown';
    case Success = 'success';
    case Failed = 'failed';
    case Pending = 'pending';
    case Confirming = 'confirming';

    case Processing = "processing";
    case Delivered = 'delivered';
    case Undelivered = "undelivered";
    case Shipped = 'shipped';
    case Returned = 'returned';
    case Supplied = 'supplied';
    case Cancelled = 'cancelled';

    case Delivering = 'delivering';

    case Shipping = 'shipping';

    case Paid = "paid";

    case Supplying = 'supplying';

}

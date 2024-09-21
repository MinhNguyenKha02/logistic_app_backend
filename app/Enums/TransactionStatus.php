<?php

namespace App\Enums;

enum TransactionStatus: string{
    case Success = "success";
    case Failed = "failed";
    case Pending = "pending";

    case Processing = "processing";
    case Delivered = "delivered";
    case Returned = 'returned';
    case Supply = 'supply';
}

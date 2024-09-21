<?php

namespace App\Enums;

enum ReceiptStatus:string{
    case SUCCESS = 'success';
    case FAIL = 'fail';

    case PENDING = 'pending';

    case Processing = "processing";

}

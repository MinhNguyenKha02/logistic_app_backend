<?php

namespace App\Enums;

enum TransactionType:string {
    case Purchase = "purchase";

    case Return = "return";
    case Supply = "supply";
    case Consume = "consume";
    case Delivery = "delivery";

}

<?php

namespace App\Enums;

enum Role:string{

    case Manager = 'manager';
    case Admin = 'admin';

    case Employee = 'employee';

    case Supplier = 'supplier';

    case Customer = 'customer';


}

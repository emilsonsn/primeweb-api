<?php

namespace App\Enums;

enum RolesEnum: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case Consultant = 'Consultant';
    case Seller = 'Seller';
}

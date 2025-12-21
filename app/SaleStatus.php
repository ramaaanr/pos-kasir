<?php

namespace App\Enums;

enum SaleStatus: string
{
    case UTANG = 'utang';
    case SEBAGIAN = 'sebagian';
    case LUNAS = 'lunas';
}

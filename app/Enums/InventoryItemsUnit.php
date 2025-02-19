<?php

namespace App\Enums;

enum InventoryItemsUnit: int
{
    case Box = 1;
    case Packet = 2;
    case Liter = 3;
    case Inch = 4;
    case Feet = 5;
    case Piece = 6;
    case Dozen  = 7;
    case Pair  = 8;
    case Meter  = 9;
}

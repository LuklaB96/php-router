<?php
namespace App\Lib\Database\Enums;

enum RelationType: string
{
    case ONE_TO_MANY = "OneToMany";
    case MANY_TO_ONE = "ManyToOne";
    case MANY_TO_MANY = "ManyToMany";
    case ONE_TO_ONE = "OneToOne";
}
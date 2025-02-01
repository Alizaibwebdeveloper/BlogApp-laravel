<?php
    
namespace App;


enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Rejected = 'rejected';
}

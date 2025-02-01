<?php

namespace App;

enum UserType : string
{
    case Admin = 'admin';
    case SuperAdmin = 'superAdmin';
    case User = 'user';
    case Guest = 'guest';
}


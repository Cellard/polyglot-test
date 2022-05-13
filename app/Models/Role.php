<?php

namespace App\Models;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Editor = 'editor';
}

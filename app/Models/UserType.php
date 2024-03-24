<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    const USER_TYPE_TRIAL = 'trial';
    const USER_TYPE_PAID = 'paid';
}

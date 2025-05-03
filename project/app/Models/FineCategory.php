<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash; // If using DB:: facade for Hashing

class FineCategory extends Model
{
    // Eloquent assumes table name 'users' by default, so $table is optional

    // Fields allowed for mass assignment
    protected $fillable = [
        'resident_id',
        'first_name',
        'last_name',
        'middle_name',
        'phone_number',
        'email',
        'password',
        'payment_method',
        'payment_status',
        'payment_amount',
        'payment_method',
    ];



} 
<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Resident;
use App\Models\FineCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash; // If using DB:: facade for Hashing

class Fines extends Model
{
    // Eloquent assumes table name 'users' by default, so $table is optional

    // Fields allowed for mass assignment
    protected $fillable = [
        'fine_category_id',
        'resident_id',
        'room_id',
        'fine_amount',
        'fine_date',
        'fine_status',
        'payment_date',
        'payment_method',
        'payment_status',
        'payment_amount',
        'payment_method',
        'payment_status',
        'payment_amount',
        'payment_method',
    ];

    // Fields to hide when serializing (e.g., for API responses)
    protected $hidden = [
        'password',
        // 'remember_token', // If you add remember token functionality later
    ];

    // Eloquent handles timestamps automatically
    public $timestamps = true;

    /**
     * Automatically hash the password when setting it.
     * Eloquent Mutator: set<AttributeName>Attribute
     */
    public function setPasswordAttribute(string $value): void
    {
        // Requires DB facade to be set up globally or use PHP's password_hash
        // Using PHP's built-in function is safer if not using full Laravel context
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
        // Or if using DB facade: $this->attributes['password'] = Hash::make($value);
    }

    /**
     * The roles assigned to the user.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function fineCategory(): BelongsTo
    {
        return $this->belongsTo(FineCategory::class, 'fine_category_id');
    }
} 
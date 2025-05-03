<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    // Explicitly define the table name if it doesn't follow plural convention
    protected $table = 'roles';

    // Allow mass assignment for these fields
    protected $fillable = ['name', 'description'];

    // Eloquent automatically handles created_at/updated_at if they exist
    public $timestamps = true;

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        // Args: Related model, pivot table name, foreign key of this model, foreign key of related model
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }
} 
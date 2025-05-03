<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash; // If using DB:: facade for Hashing

class User extends Model
{
    // Eloquent assumes table name 'users' by default, so $table is optional

    // Fields allowed for mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'phone_number',
        'email',
        'password',
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
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string|array $roleName The name of the role(s) to check.
     * @return bool
     */
    public function hasRole(string|array $roleName): bool
    {
        $roleNames = is_array($roleName) ? $roleName : [$roleName];
        foreach ($this->roles as $role) {
            if (in_array($role->name, $roleNames)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has a specific permission (directly or through roles).
     *
     * @param string $permissionName The name of the permission to check.
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->roles as $role) {
            // Check if the role itself has the permission
            // Eager load permissions for efficiency if checking many permissions
            // $role->loadMissing('permissions'); // Can be done in controller
            foreach ($role->permissions as $permission) {
                if ($permission->name === $permissionName) {
                    return true;
                }
            }
        }
        // Optionally, check for direct permissions assigned to the user if you add that feature
        return false;
    }

    // You can add more complex permission logic here, like checking permission inheritance

} 
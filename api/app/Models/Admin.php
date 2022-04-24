<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function assign(Permission $permission)
    {
        $exists = $this->permissions()
            ->where('permission_id', $permission->id)
            ->exists();

        if ($exists) {
            return true;
        }

        return $this->permissions()->attach($permission);
    }

    public function revoke(Permission $permission)
    {
        return $this->permissions()->detach($permission);
    }

    public function revokeAll()
    {
        return $this->permissions()->detach();
    }

    public function getPermissionsByUser($userId)
    {
        $directPermission    = $this->getDirectPermissionsByUser($userId);
        $inheritedPermission = $this->getInheritedPermissionsByUser($userId);

        return array_merge($directPermission, $inheritedPermission);
    }
}

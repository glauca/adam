<?php

namespace App\Models;

use App\Models\AdminTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use AdminTrait;
    use HasFactory;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->where('type', Permission::TYPE_PERMISSION);
    }

    public function roles()
    {
        return $this->belongsToMany(Permission::class)
            ->where('type', Permission::TYPE_ROLE);
    }

    public function getPermissionsByUser($userId)
    {
        $directPermission    = $this->getDirectPermissionsByUser($userId);
        $inheritedPermission = $this->getInheritedPermissionsByUser($userId);

        return array_merge($directPermission, $inheritedPermission);
    }
}

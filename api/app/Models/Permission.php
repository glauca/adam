<?php

namespace App\Models;

use App\Models\PermissionTrait;
use ErrorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    use PermissionTrait;

    const TYPE_ROLE       = 1;
    const TYPE_PERMISSION = 2;

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class);
    }

    public function children()
    {
        return $this->belongsToMany(Permission::class, 'permission_children', 'parent_id', 'child_id');
    }

    public function addChild(Permission $child)
    {
        if ($this->type == Permission::TYPE_PERMISSION && $child->type == Permission::TYPE_ROLE) {
            throw new ErrorException('Cannot add a role as a child of a permission.');
        }

        return $this->children()->save($child);
    }

    public static function root()
    {
        return Permission::ofType(Permission::TYPE_ROLE)
            ->where('name', 'root')
            ->first();
    }
}

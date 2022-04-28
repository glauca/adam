<?php

namespace App\Models;

use ErrorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    use HasFactory;

    const TYPE_ROLE       = 1;
    const TYPE_PERMISSION = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'name'];

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

        return DB::table('permission_children')->insert([
            'parent_id' => $child->id,
            'child_id'  => $child->id,
        ]);
    }

    public static function root()
    {
        return Permission::ofType(Permission::TYPE_ROLE)
            ->where('name', 'root')
            ->first();
    }

    public static function getChildren(Permission $permission)
    {
        $children = $permission->children;

        $parents = [];
        if ($children) {
            $permission->children = $children;

            foreach ($children as $child) {
                if (empty($parents[$permission->name])) {
                    $parents[$permission->name] = [];
                }
                $parents[$permission->name][] = $child->name;

                $items   = Permission::getChildren($child);
                $parents = $parents + $items;
            }
        }

        return $parents;
    }

    public function getPermissions($permission, $parents)
    {
        $items = [];

        if (isset($parents[$permission])) {
            foreach ($parents[$permission] as $parent) {
                $items[] = $parent;
                if (isset($parents[$parent])) {
                    $items = array_merge($items, $this->getPermissions($parent, $parents));
                }
            }
        }

        return $items;
    }
}

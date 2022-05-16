<?php

namespace App\Models;

trait PermissionTrait
{
    /**
     * 返回权限的所有子权限 包括继承与非继承的权限
     *
     * @example Permission::getAllPerms('root', $parents);
     *
     * @param  string $perm    [description]
     * @param  array  $parents [description]
     * @return [type]          [description]
     */
    public static function getAllPermsName(string $perm, array $parents)
    {
        $items = [];

        if (isset($parents[$perm])) {
            foreach ($parents[$perm] as $parent) {
                $items[] = $parent;
                if (isset($parents[$parent])) {
                    $items = array_merge($items, static::getAllPermsName($parent, $parents));
                }
            }
        }

        return $items;
    }

    /**
     * 返回权限下面的所有子权限
     *
     * @example Permission::getAllDirectPerms($root);
     *
     * @param  Permission $permission [description]
     * @return array
     */
    public static function getDirectPerms(Permission $node)
    {
        $children = $node->children;

        $parents = [];
        if (count($children)) {
            foreach ($children as $child) {
                if (empty($parents[$node->name])) {
                    $parents[$node->name] = [];
                }
                $parents[$node->name][] = $child->name;

                $items   = static::getDirectPerms($child);
                $parents = $parents + $items;
            }
        }

        return $parents;
    }
}

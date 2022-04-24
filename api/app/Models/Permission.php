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

    public function admins()
    {
        return $this->belongsToMany(Admin::class);
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
}

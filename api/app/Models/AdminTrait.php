<?php

namespace App\Models;

trait AdminTrait
{
    public function assign(Permission $perm)
    {
        $this->revoke($perm);

        return $this->permissions()->attach($perm);
    }

    public function revoke(Permission $perm)
    {
        return $this->permissions()->detach($perm);
    }

    public function revokeAll()
    {
        return $this->permissions()->detach();
    }
}

<?php

namespace App\Traits;

use Spatie\Permission\Models\Permission;

trait HasSlugPermissions
{
    /**
     * Check if user has permission by slug (checks through roles)
     */
    public function canBySlug($slug)
    {
        $permission = Permission::where('slug', $slug)->first();
        
        if (!$permission) {
            return false;
        }

        return $this->hasPermissionTo($permission->name);
    }

    /**
     * Check if user has any of the given permission slugs
     */
    public function canAnyBySlug(array $slugs)
    {
        foreach ($slugs as $slug) {
            if ($this->canBySlug($slug)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permission slugs
     */
    public function canAllBySlug(array $slugs)
    {
        foreach ($slugs as $slug) {
            if (!$this->canBySlug($slug)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Override the can method to prioritize slug lookup
     * This works with role-based permissions automatically
     */
    public function can($abilities, $arguments = [])
    {
        if (is_string($abilities)) {
            $permission = Permission::where('slug', $abilities)->first();
            if ($permission) {
                return parent::can($permission->name, $arguments);
            }
        }

        if (is_array($abilities)) {
            $processedAbilities = [];
            foreach ($abilities as $ability) {
                $permission = Permission::where('slug', $ability)->first();
                $processedAbilities[] = $permission ? $permission->name : $ability;
            }
            return parent::can($processedAbilities, $arguments);
        }

        return parent::can($abilities, $arguments);
    }

    /**
     * Check if user has permission through a specific role by slug
     */
    public function canViaRole($permissionSlug, $roleName)
    {
        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if (!$permission) {
            return false;
        }

        return $this->hasRole($roleName) && 
               $this->roles()->where('name', $roleName)->first()
                    ->hasPermissionTo($permission->name);
    }

    /**
     * Get all permissions (by slug) that user has through all roles
     */
    public function getAllPermissionSlugs()
    {
        return $this->getAllPermissions()
            ->pluck('slug')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * Check if user's roles have permission by slug
     */
    public function hasPermissionViaRoles($permissionSlug)
    {
        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if (!$permission) {
            return false;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermissionTo($permission->name)) {
                return true;
            }
        }

        return false;
    }
}
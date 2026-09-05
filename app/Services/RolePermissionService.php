<?php

namespace App\Services;

use App\Models\User;

class RolePermissionService
{
    public const PERMISSION_ACCESS_ADMIN = 'access_admin';
    public const PERMISSION_MANAGE_USERS = 'manage_users';
    public const PERMISSION_MANAGE_ROLES = 'manage_roles';
    public const PERMISSION_MANAGE_CONTENT = 'manage_content';
    public const PERMISSION_MANAGE_ASSESSMENTS = 'manage_assessments';
    public const PERMISSION_MANAGE_MEDIA = 'manage_media';
    public const PERMISSION_VIEW_REPORTS = 'view_reports';
    public const PERMISSION_MANAGE_SETTINGS = 'manage_settings';

    /**
     * @return array<string, array<int, string>>
     */
    public function matrix(): array
    {
        return [
            User::ROLE_SUPER_ADMIN => [
                self::PERMISSION_ACCESS_ADMIN,
                self::PERMISSION_MANAGE_USERS,
                self::PERMISSION_MANAGE_ROLES,
                self::PERMISSION_MANAGE_CONTENT,
                self::PERMISSION_MANAGE_ASSESSMENTS,
                self::PERMISSION_MANAGE_MEDIA,
                self::PERMISSION_VIEW_REPORTS,
                self::PERMISSION_MANAGE_SETTINGS,
            ],
            User::ROLE_ADMIN => [
                self::PERMISSION_ACCESS_ADMIN,
                self::PERMISSION_MANAGE_USERS,
                self::PERMISSION_MANAGE_CONTENT,
                self::PERMISSION_MANAGE_ASSESSMENTS,
                self::PERMISSION_MANAGE_MEDIA,
                self::PERMISSION_VIEW_REPORTS,
            ],
            User::ROLE_TEACHER => [
                self::PERMISSION_MANAGE_CONTENT,
                self::PERMISSION_MANAGE_ASSESSMENTS,
                self::PERMISSION_MANAGE_MEDIA,
                self::PERMISSION_VIEW_REPORTS,
            ],
            User::ROLE_LEARNER => [],
        ];
    }

    public function allows(User $user, string $permission): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        return in_array($permission, $this->matrix()[$user->role] ?? [], true);
    }

    /**
     * @return array<string, string>
     */
    public function permissionLabels(): array
    {
        return [
            self::PERMISSION_ACCESS_ADMIN => 'Access Admin Console',
            self::PERMISSION_MANAGE_USERS => 'Manage Users',
            self::PERMISSION_MANAGE_ROLES => 'Manage Roles',
            self::PERMISSION_MANAGE_CONTENT => 'Manage Learning Content',
            self::PERMISSION_MANAGE_ASSESSMENTS => 'Manage Assessments',
            self::PERMISSION_MANAGE_MEDIA => 'Manage Media',
            self::PERMISSION_VIEW_REPORTS => 'View Reports',
            self::PERMISSION_MANAGE_SETTINGS => 'Manage Platform Settings',
        ];
    }
}

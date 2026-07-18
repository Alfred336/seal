<?php

namespace App\Enums;

enum Permission: string
{
    case PostsView = 'posts.view';
    case PostsCreate = 'posts.create';
    case PostsUpdateOwn = 'posts.update-own';
    case PostsDeleteOwn = 'posts.delete-own';
    case PostsManageAll = 'posts.manage-all';
    case PostsPublish = 'posts.publish';
    case CategoriesManage = 'categories.manage';
    case TagsManage = 'tags.manage';
    case ServicesView = 'services.view';
    case ServicesManage = 'services.manage';
    case ProjectsView = 'projects.view';
    case ProjectsManage = 'projects.manage';
    case ContactSubmissionsView = 'contact-submissions.view';
    case ContactSubmissionsUpdate = 'contact-submissions.update';
    case CallRequestsView = 'call-requests.view';
    case CallRequestsUpdate = 'call-requests.update';
    case ProjectRequestsView = 'project-requests.view';
    case ProjectRequestsUpdate = 'project-requests.update';
    case SubscriptionsView = 'subscriptions.view';
    case SubscriptionsManage = 'subscriptions.manage';
    case UsersView = 'users.view';
    case UsersManage = 'users.manage';
    case RolesManage = 'roles.manage';
    case CareersManage = 'careers.manage';
    case BackupsManage = 'backups.manage';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

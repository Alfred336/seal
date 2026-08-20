<?php

namespace App\Models;

use App\Enums\PlanInquiryStatus;
use Illuminate\Database\Eloquent\Model;

class PlanInquiry extends Model
{
    protected $fillable = [
        'plan',
        'full_name',
        'business_name',
        'position',
        'phone',
        'email',
        'business_category',
        'website_purpose',
        'number_of_pages',
        'needs_cms',
        'needs_blog',
        'has_domain',
        'has_logo',
        'has_content',
        'reference_website',
        'additional_requirements',
        'project_type',
        'expected_users',
        'ecommerce',
        'online_payments',
        'booking_system',
        'user_accounts',
        'admin_dashboard',
        'api_integration',
        'sms_notifications',
        'existing_system',
        'project_deadline',
        'budget_range',
        'project_description',
        'additional_message',
        'status',
        'notes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'number_of_pages' => 'integer',
            'needs_cms' => 'boolean',
            'needs_blog' => 'boolean',
            'has_domain' => 'boolean',
            'has_logo' => 'boolean',
            'has_content' => 'boolean',
            'ecommerce' => 'boolean',
            'online_payments' => 'boolean',
            'booking_system' => 'boolean',
            'user_accounts' => 'boolean',
            'admin_dashboard' => 'boolean',
            'api_integration' => 'boolean',
            'sms_notifications' => 'boolean',
            'status' => PlanInquiryStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
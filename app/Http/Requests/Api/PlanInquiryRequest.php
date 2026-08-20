<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan' => [
                'required',
                'string',
                Rule::in([
                    'starter',
                    'professional',
                    'enterprise',
                ]),
            ],

            'full_name' => [
                'required',
                'string',
                'max:100',
            ],

            'business_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'position' => [
                'nullable',
                'string',
                'max:100',
            ],

            'phone' => [
                'required',
                'string',
                'max:40',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            // Starter / Professional

            'business_category' => [
                'required_if:plan,starter,professional',
                'nullable',
                'string',
                'max:100',
            ],

            'website_purpose' => [
                'required_if:plan,starter,professional',
                'nullable',
                'string',
                'max:3000',
            ],

            'number_of_pages' => [
                'nullable',
                'integer',
                'min:1',
                'max:500',
            ],

            'needs_cms' => ['nullable', 'boolean'],
            'needs_blog' => ['nullable', 'boolean'],
            'has_domain' => ['nullable', 'boolean'],
            'has_logo' => ['nullable', 'boolean'],
            'has_content' => ['nullable', 'boolean'],

            'reference_website' => [
                'nullable',
                'url',
                'max:500',
            ],

            'additional_requirements' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'additional_message' => [
                'nullable',
                'string',
                'max:5000',
            ],

            // Enterprise

            'project_type' => [
                'required_if:plan,enterprise',
                'nullable',
                'string',
                'max:120',
            ],

            'expected_users' => [
                'nullable',
                'string',
                'max:120',
            ],

            'ecommerce' => ['nullable', 'boolean'],
            'online_payments' => ['nullable', 'boolean'],
            'booking_system' => ['nullable', 'boolean'],
            'user_accounts' => ['nullable', 'boolean'],
            'admin_dashboard' => ['nullable', 'boolean'],
            'api_integration' => ['nullable', 'boolean'],
            'sms_notifications' => ['nullable', 'boolean'],

            'existing_system' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'project_deadline' => [
                'nullable',
                'string',
                'max:120',
            ],

            'budget_range' => [
                'nullable',
                'string',
                'max:120',
            ],

            'project_description' => [
                'required_if:plan,enterprise',
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
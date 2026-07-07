<?php

namespace Tests\Feature;

use App\Mail\CallRequestConfirmation;
use App\Mail\ContactSubmissionConfirmation;
use App\Mail\NewCallRequest;
use App\Mail\NewContactSubmission;
use App\Mail\NewProjectRequest;
use App\Mail\ProjectRequestConfirmation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubmissionNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_contact_submission_sends_emails(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $postData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'company' => 'Doe Inc.',
            'phone' => '+123456789',
            'project_type' => 'Web App',
            'message' => 'Hello, I want a website.',
        ];

        $response = $this->postJson(route('api.contact.store'), $postData);

        $response->assertCreated();

        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'johndoe@example.com',
            'name' => 'John Doe',
        ]);

        // Assert visitor confirmation email was sent
        Mail::assertQueued(ContactSubmissionConfirmation::class, function ($mail) use ($postData) {
            return $mail->hasTo('johndoe@example.com') && $mail->submission->name === 'John Doe';
        });

        // Assert staff notification email was sent to admin/support
        $admin = User::where('email', 'admin@sealtech.test')->firstOrFail();
        Mail::assertQueued(NewContactSubmission::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_call_request_sends_emails(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $postData = [
            'full_name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'phone' => '+987654321',
            'preferred_date' => now()->addDays(2)->format('Y-m-d'),
            'notes' => 'Call me at 3 PM please.',
        ];

        $response = $this->postJson(route('api.calls.store'), $postData);

        $response->assertCreated();

        $this->assertDatabaseHas('call_requests', [
            'email' => 'janesmith@example.com',
            'full_name' => 'Jane Smith',
        ]);

        // Assert visitor confirmation email was sent
        Mail::assertQueued(CallRequestConfirmation::class, function ($mail) use ($postData) {
            return $mail->hasTo('janesmith@example.com') && $mail->requestModel->full_name === 'Jane Smith';
        });

        // Assert staff notification email was sent to admin/support
        $admin = User::where('email', 'admin@sealtech.test')->firstOrFail();
        Mail::assertQueued(NewCallRequest::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_project_request_sends_emails(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $postData = [
            'full_name' => 'Alice Wonderland',
            'email' => 'alice@example.com',
            'project_type' => 'Mobile App',
            'details' => 'I want a beautiful iOS app built.',
        ];

        $response = $this->postJson(route('api.project-request.store'), $postData);

        $response->assertCreated();

        $this->assertDatabaseHas('project_requests', [
            'email' => 'alice@example.com',
            'full_name' => 'Alice Wonderland',
        ]);

        // Assert visitor confirmation email was sent
        Mail::assertQueued(ProjectRequestConfirmation::class, function ($mail) use ($postData) {
            return $mail->hasTo('alice@example.com') && $mail->requestModel->full_name === 'Alice Wonderland';
        });

        // Assert staff notification email was sent to admin/support
        $admin = User::where('email', 'admin@sealtech.test')->firstOrFail();
        Mail::assertQueued(NewProjectRequest::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }
}

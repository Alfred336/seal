<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ContactRequest;
use App\Mail\ContactSubmissionConfirmation;
use App\Mail\NewContactSubmission;
use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends ApiController
{
    public function store(ContactRequest $request): JsonResponse
    {
        $submission = ContactSubmission::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        // Send confirmation to the visitor who submitted the form
        Mail::to($submission->email)->send(new ContactSubmissionConfirmation($submission));

        // Notify the admin/support team
        $recipients = User::role(['admin', 'support'])->get();
        if ($recipients->isEmpty()) {
            $admin = User::where('email', 'admin@sealtech.test')->first();
            if ($admin) {
                $recipients = collect([$admin]);
            }
        }

        if ($recipients->isNotEmpty()) {
            Mail::to($recipients)->send(new NewContactSubmission($submission));
        }

        return response()->json(['message' => 'Message received. We will be in touch soon.'], 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ProjectRequestRequest;
use App\Mail\NewProjectRequest;
use App\Mail\ProjectRequestConfirmation;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ProjectRequestController extends ApiController
{
    public function store(ProjectRequestRequest $request): JsonResponse
    {
        $requestModel = ProjectRequest::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        // Send confirmation to the visitor who submitted the form
        Mail::to($requestModel->email)->send(new ProjectRequestConfirmation($requestModel));

        // Notify the admin/support team
        $recipients = User::role(['admin', 'support'])->get();
        if ($recipients->isEmpty()) {
            $admin = User::where('email', 'admin@sealtech.test')->first();
            if ($admin) {
                $recipients = collect([$admin]);
            }
        }

        if ($recipients->isNotEmpty()) {
            Mail::to($recipients)->send(new NewProjectRequest($requestModel));
        }

        return response()->json(['message' => 'Project request received. Our team will review it and get back to you.'], 201);
    }
}

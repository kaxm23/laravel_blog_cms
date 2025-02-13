<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Jobs\SendNewsletterJob;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers',
        ]);

        Newsletter::create([
            'email' => $request->email,
            'status' => 'subscribed',
        ]);

        return response()->json([
            'message' => 'Successfully subscribed to newsletter',
        ]);
    }

    public function send(Request $request)
    {
        $newsletter = Newsletter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'schedule_date' => $request->schedule_date,
        ]);

        SendNewsletterJob::dispatch($newsletter)
            ->delay($request->schedule_date);

        return back()->with('status', 'Newsletter scheduled successfully');
    }
}
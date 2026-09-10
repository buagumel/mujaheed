<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\BroadcastCampaign;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBroadcastController extends Controller
{
    public function index()
    {
        $campaigns = BroadcastCampaign::with('sender')->latest()->paginate(15);
        $users = User::where('role', 'customer')->with('wallet')->orderBy('name')->get();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->where('status', 'active')->count();

        return view('admin.broadcast.index', compact('campaigns', 'users', 'totalCustomers', 'activeCustomers'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:5'],
            'target_audience' => ['required', 'in:all,active,inactive,funded,unfunded,selected'],
            'channel' => ['required', 'in:email,notification,both'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $query = User::where('role', 'customer');

        if ($validated['target_audience'] === 'active') {
            $query->where('status', 'active');
        } elseif ($validated['target_audience'] === 'inactive') {
            $query->where('status', '!=', 'active');
        } elseif ($validated['target_audience'] === 'funded') {
            $query->whereHas('wallet', function ($wq) {
                $wq->where('balance', '>', 0);
            });
        } elseif ($validated['target_audience'] === 'unfunded') {
            $query->whereHas('wallet', function ($wq) {
                $wq->where('balance', '<=', 0);
            });
        } elseif ($validated['target_audience'] === 'selected') {
            if (empty($validated['user_ids'])) {
                return back()->withErrors(['user_ids' => 'Please tick and select at least one customer from the list below.']);
            }
            $query->whereIn('id', $validated['user_ids']);
        }

        $recipients = $query->get();
        $recipientCount = $recipients->count();

        if ($recipientCount === 0) {
            return back()->withErrors(['target_audience' => 'No customers match the selected audience filter.']);
        }

        $platformName = SystemSetting::get('platform_name', 'VTU Express');

        foreach ($recipients as $recipient) {
            // 1. Send In-App Notification
            if (in_array($validated['channel'], ['notification', 'both'])) {
                AppNotification::create([
                    'user_id' => $recipient->id,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'type' => 'broadcast',
                    'is_read' => false,
                ]);
            }

            // 2. Send Email
            if (in_array($validated['channel'], ['email', 'both']) && !empty($recipient->email)) {
                $emailHtml = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #e0e0e0; border-radius: 12px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <h2 style='color: #1877F2; margin: 0;'>{$platformName}</h2>
                        <p style='color: #637381; font-size: 13px; margin-top: 4px;'>Official Announcement</p>
                    </div>
                    <h3 style='color: #1C252E; margin-bottom: 12px;'>{$validated['title']}</h3>
                    <div style='font-size: 15px; line-height: 1.6; color: #454F5B; white-space: pre-wrap;'>{$validated['message']}</div>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 24px 0;' />
                    <p style='font-size: 11px; color: #919EAB; text-align: center;'>You received this broadcast because you have an account on {$platformName}.</p>
                </div>";

                MailConfigService::sendRaw($recipient->email, "[{$platformName}] {$validated['title']}", $emailHtml);
            }
        }

        $campaign = BroadcastCampaign::create([
            'sent_by' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'channel' => $validated['channel'],
            'recipients_count' => $recipientCount,
        ]);

        AuditLog::record(
            'broadcast_sent',
            "Admin dispatched broadcast '{$validated['title']}' to {$recipientCount} recipients via {$validated['channel']}.",
            Auth::id(),
            ['campaign_id' => $campaign->id, 'recipients_count' => $recipientCount]
        );

        return back()->with('success', "Broadcast successfully delivered to {$recipientCount} customer(s)!");
    }
}

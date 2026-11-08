<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $publicKey = AdminSetting::get('vapid_public_key');
        $privateKey = AdminSetting::get('vapid_private_key');
        $subject = AdminSetting::get('vapid_subject', 'mailto:admin@adtec.edu.my');

        if ($publicKey && $privateKey) {
            $auth = [
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ];

            try {
                $this->webPush = new WebPush($auth);
            } catch (\Exception $e) {
                Log::error('WebPush initialization failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send emergency notification to all registered push subscribers.
     */
    public function sendNotification(string $title, string $message, string $url = '/', array $extra = []): int
    {
        if (!$this->webPush) {
            Log::warning('WebPush is not configured with VAPID keys.');
            return 0;
        }

        $subscriptions = PushSubscription::all();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $message,
            'url' => $url,
            'icon' => '/icons/icon-192.png',
            'badge' => '/icons/badge-72.png',
            'tag' => 'adtec-emergency-alert',
            'timestamp' => now()->timestamp,
            'extra' => $extra,
        ]);

        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
                ]);

                $this->webPush->queueNotification($subscription, $payload);
            } catch (\Exception $e) {
                Log::warning('Error queuing push notification: ' . $e->getMessage());
            }
        }

        $successCount = 0;
        $staleIds = [];

        try {
            foreach ($this->webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $successCount++;
                } else {
                    Log::info("Push notification failed for {$endpoint}: {$report->getReason()}");
                    if ($report->isSubscriptionExpired()) {
                        $staleIds[] = $endpoint;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error flushing web push notifications: ' . $e->getMessage());
        }

        // Clean up expired subscriptions
        if (!empty($staleIds)) {
            PushSubscription::whereIn('endpoint', $staleIds)->delete();
        }

        return $successCount;
    }
}


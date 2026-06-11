<?php

namespace App\Jobs;

use App\Models\DeviceToken;
use App\Models\UserNotification;
use Google\Client as GoogleClient;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tokens;

    protected $topic;

    protected $title;

    protected $body;

    protected $data;

    public function __construct(array $tokens, ?string $topic, string $title, string $body, array $data = [])
    {
        $this->tokens = $tokens;
        $this->topic = $topic;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function handle(): void
    {
        // 🔑 مصادقة Google
        $client = new GoogleClient;
        $client->setAuthConfig(base_path(env('FIREBASE_CREDENTIALS')));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        $projectId = 'school-auth-app';
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $http = new HttpClient;

        $successCount = 0;
        $failureCount = 0;

        if ($this->topic) {
            // 🚀 إرسال لموضوع كامل (Topic)
            $payload = [
                'message' => [
                    'topic' => $this->topic,
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'data' => $this->data,
                ],
            ];

            $http->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);
            $successCount++; // اعتبرناها نجحت
        } elseif (! empty($this->tokens)) {
            // 🔁 إرسال لكل توكن (لأن v1 API ما بدعم tokens array)
            foreach ($this->tokens as $token) {
                $payload = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $this->title,
                            'body' => $this->body,
                        ],
                        'data' => $this->data,
                    ],
                ];

                try {
                    $res = $http->post($url, [
                        'headers' => [
                            'Authorization' => "Bearer {$accessToken}",
                            'Content-Type' => 'application/json',
                        ],
                        'json' => $payload,
                    ]);

                    $resBody = json_decode($res->getBody(), true);

                    if (isset($resBody['name'])) {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }
                } catch (\Exception $e) {
                    $failureCount++;

                    // 🔥 إذا التوكن غير صالح، احذفه
                    if (str_contains($e->getMessage(), 'NotRegistered') || str_contains($e->getMessage(), 'InvalidRegistration')) {
                        DeviceToken::where('token', $token)->delete();
                    }

                    \Log::error('❌ خطأ عند إرسال FCM: '.$e->getMessage());
                }
            }
        }

        // 📝 سجل الإشعار في جدول user_notifications
        UserNotification::create([
            'title' => $this->title,
            'body' => $this->body,
            'data' => json_encode($this->data),
            'success_count' => $successCount,
            'failure_count' => $failureCount,
        ]);
    }
}

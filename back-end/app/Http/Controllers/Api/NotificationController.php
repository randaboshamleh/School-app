<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Jobs\SendFcmJob;
use App\Models\DeviceToken;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\SchoolNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $user = $req->user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unread = UserNotification::where('user_id', $user->id)->where('is_read', false)->count();

        return response()->json([
            'data' => $notifications->items(),
            'meta' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'unread_count' => $unread,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request)
    {
        $validated = $request->validated();

        $notification = DatabaseNotification::create([
            'id' => Str::uuid(),
            'type' => $validated['type'],
            'notifiable_type' => $validated['notifiable_type'],
            'notifiable_id' => $validated['notifiable_id'],
            'data' => $validated['data'],
            'read_at' => null,
        ]);

        return new NotificationResource($notification);
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified notification.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $req, string $id)
    {
        $notification = UserNotification::where('id', $id)
            ->where('user_id', $req->user()->id)
            ->firstOrFail();

        return response()->json($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, string $id)
    {
        $notification = DatabaseNotification::find($id);

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->update($request->validated());

        return new NotificationResource($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notification = DatabaseNotification::find($id);

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }

    public function sendNotificationToAllUsers()
    {
        $users = User::whereHas('fcmTokens')->get();

        foreach ($users as $user) {
            $user->notify(new SchoolNotification);
        }

        return response()->json(['message' => 'Notification sent to all users.']);
    }

    public function unreadCount(Request $req)
    {
        $count = UserNotification::where('user_id', $req->user()->id)->where('is_read', false)->count();

        return response()->json(['unread' => $count]);
    }

    public function markRead(Request $req, $id)
    {
        $n = UserNotification::where('id', $id)->where('user_id', $req->user()->id)->firstOrFail();
        $n->is_read = true;
        $n->save();

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $req)
    {
        UserNotification::where('user_id', $req->user()->id)->where('is_read', false)->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function sendFlexible(Request $req)
    {
        $req->validate([
            'title' => 'required|string',
            'body' => 'nullable|string',
            'user_ids' => 'nullable|array',   // مجموعة طلاب
            'user_id' => 'nullable|integer', // طالب واحد
            'topic' => 'nullable|string',  // موضوع كامل
            'data' => 'nullable|array',
            'platform' => 'nullable|string|in:android,ios,web', // 🔹 فلترة المنصة
        ]);

        $title = $req->title;
        $body = $req->body;
        $data = $req->data ?? [];

        // 1️⃣ إرسال لموضوع كامل إذا topic موجود
        if ($req->filled('topic')) {
            SendFcmJob::dispatch([], $req->topic, $title, $body, $data);

            return response()->json(['ok' => true, 'message' => "Notification sent to topic: {$req->topic}"]);
        }

        $tokens = [];

        // 2️⃣ إرسال لمجموعة طلاب
        if ($req->filled('user_ids')) {
            $tokens = DeviceToken::whereIn('user_id', $req->user_ids)
                ->when($req->filled('platform'), function ($q) use ($req) {
                    $q->where('platform', $req->platform);
                })
                ->pluck('token')
                ->toArray();
        }

        // 3️⃣ إرسال لطالب واحد
        if ($req->filled('user_id')) {
            $singleTokens = DeviceToken::where('user_id', $req->user_id)
                ->when($req->filled('platform'), function ($q) use ($req) {
                    $q->where('platform', $req->platform);
                })
                ->pluck('token')
                ->toArray();

            $tokens = array_merge($tokens, $singleTokens);
        }

        // حفظ الإشعارات في DB لكل مستخدم
        $userIds = array_merge($req->user_ids ?? [], $req->user_id ? [$req->user_id] : []);
        foreach ($userIds as $uid) {
            UserNotification::create([
                'user_id' => $uid,
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);
        }

        // 4️⃣ إذا في توكنات → نبعث Job
        if (! empty($tokens)) {
            SendFcmJob::dispatch($tokens, null, $title, $body, $data);
        }

        return response()->json([
            'ok' => true,
            'sent_to' => count($tokens),
            'topic' => $req->topic ?? null,
            'platform' => $req->platform ?? 'all',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * GET /api/user/messages
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $messages = Message::query()
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->with([
                'sender:id,name,profile_picture',
                'receiver:id,name,profile_picture',
            ])
            ->latest('id')
            ->paginate(30);

        return response()->json($messages);
    }

    /**
     * POST /api/user/messages   { receiver_id, body }
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id', 'different:' . $user->id],
            'body'        => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::query()->create([
            'sender_id'   => $user->id,
            'receiver_id' => $data['receiver_id'],
            'body'        => $data['body'],
        ]);

        return response()->json([
            'message' => $message->load([
                'sender:id,name,profile_picture',
                'receiver:id,name,profile_picture',
            ]),
        ], 201);
    }
}

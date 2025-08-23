<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\MessageRelation;
use Storage;

class ChatController extends Controller
{
    public function store(chatRequest $request, $item_id)
    {
        $user = Auth::user();
        $soldItem = SoldItem::where('item_id', $item_id)->first();
        $item = Item::find($item_id);
        if ($request->hasFile('img_url')) {
            $img = $request->file('img_url')->store('products', 'public');
            Message::create([
                'user_id'      => $user->id,
                'sold_item_id' => $soldItem->id,
                'text'         => $request->input('text'),
                'img_url'          => $img
            ]);
        }else{
            Message::create([
                'user_id'      => $user->id,
                'sold_item_id' => $soldItem->id,
                'text'         => $request->input('text'),
            ]);
        }

        if ($user->id == $soldItem->buyer_id) {
            $destinationUser = User::where('id', $item->user_id)->first();
            $messageRelation = MessageRelation::where('user_id', $user->id)->where('destination_user_id', $destinationUser->id)->where('sold_item_id', $soldItem->id)->first();
            if (!$messageRelation) {
                MessageRelation::Create([
                    'user_id' => $user->id,
                    'destination_user_id' => $destinationUser->id,
                    'sold_item_id' => $soldItem->id,
                    'message_count' => 1
                ]);
            } elseif ($messageRelation) {
                $update_message_count = $messageRelation->message_count + 1;
                $messageRelation->update([
                    'user_id' => $user->id,
                    'destination_user_id' => $destinationUser->id,
                    'sold_item_id' => $soldItem->id,
                    'message_count' => $update_message_count
                ]);
            }
        } elseif ($user->id == $soldItem->seller_id) {
            $destinationUser = User::where('id', $soldItem->buyer_id)->first();
            $messageRelation = MessageRelation::where('user_id', $user->id)->where('destination_user_id', $destinationUser->id)->where('sold_item_id', $soldItem->id)->first();
            if (!$messageRelation) {
                MessageRelation::Create([
                    'user_id' => $user->id,
                    'destination_user_id' => $destinationUser->id,
                    'sold_item_id' => $soldItem->id,
                    'message_count' => 1
                ]);
            } elseif ($messageRelation) {
                $update_message_count = $messageRelation->message_count + 1;
                $messageRelation->update([
                    'user_id' => $user->id,
                    'destination_user_id' => $destinationUser->id,
                    'sold_item_id' => $soldItem->id,
                    'message_count' => $update_message_count
                ]);
            }
        }
        return redirect()->route('show', ['item_id' => $item_id]);
    }

    public function update(chatRequest $request)
    {
        $message = $request->only(['text']);
        Message::find($request->id)->update($message);
        return redirect()->back();
    }

    public function delete(Request $request)
    {
        Message::find($request->id)->delete();
        return redirect()->back();
    }

}

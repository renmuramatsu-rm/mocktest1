<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Profile;
use App\Models\SoldItem;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\UserReview;
use App\Models\MessageRelation;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewMail;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $tab    = $request->query('tab', 'recommend');
        $search = $request->query('search');
        $query  = Item::query();
        $query->where('user_id', '<>', Auth::id());

        if ($tab === 'mylist') {
            $query->whereIn('id', function ($query) {
                $query->select('item_id')
                    ->from('likes')
                    ->where('user_id', Auth::id());
            });
        }
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        $items = $query->get();
        return view('index', compact('items', 'tab', 'search'));
    }

    public function search(Request $request)
    {
        $search_word = $request->input('search');
        $query = Item::query();
        $query = Item::scopeItem($query, $search_word);

        $items = $query->get();
        return view('index', compact('items'));
    }

    public function show($item_id)
    {
        $user       = Auth::user();
        $item       = Item::with(['condition'])->find($item_id);
        $soldItem   = SoldItem::where('item_id', ($item_id))->first();
        if ($item->sold() && $item->sold_item->status == 'progress') {
            if ($item->sold_item->buyer_id == $user->id) {
                $seller = User::where('id', $soldItem->seller_id)->first();
                $otherItems = SoldItem::with('item')
                    ->where('buyer_id', $user->id)
                    ->where('status', 'progress')
                    ->where('item_id', '<>', $item_id)
                    ->withMax('messages', 'updated_at')
                    ->orderBy('messages_max_updated_at', 'desc')
                    ->get();
                $messages = Message::where('sold_item_id', $soldItem->id)->get();
                $resetMessageCount = MessageRelation::where('destination_user_id', $user->id)->where('sold_item_id', $item->sold_item->id)->first();
                if (!$resetMessageCount == 0) {
                    $resetMessageCount->update(['message_count' => 0]);
                }
                return view('purchaseChat', compact('user', 'item', 'otherItems', 'messages', 'seller', 'soldItem'));
            } elseif ($item->sold_item->seller_id == $user->id) {
                $buyer = User::where('id', $soldItem->buyer_id)->first();
                $otherItems = SoldItem::with('item')
                    ->where('seller_id', $user->id)
                    ->where('status', 'progress')
                    ->where('item_id', '<>', $item_id)
                    ->withMax('messages', 'updated_at')
                    ->orderBy('messages_max_updated_at', 'desc')
                    ->get();
                $messages = Message::where('sold_item_id', $soldItem->id)->get();
                $resetMessageCount = MessageRelation::where('destination_user_id', $user->id)->where('sold_item_id', $item->sold_item->id)->first();
                if (!$resetMessageCount == 0) {
                    $resetMessageCount->update(['message_count' => 0]);
                }
                return view('sellChat', compact('user', 'buyer', 'item', 'otherItems', 'messages'));
            }
        }
        if ($item->sold() && $item->sold_item->status == 'purchase_completed' && $item->sold_item->seller_id == $user->id) {
            $buyer = User::where('id', $soldItem->buyer_id)->first();
            $otherItems = SoldItem::with('item')
                ->where('seller_id', $user->id)
                ->where('status', 'progress')
                ->where('item_id', '<>', $item_id)
                ->withMax('messages', 'updated_at')
                ->orderBy('messages_max_updated_at', 'desc')
                ->get();
            $messages = Message::where('sold_item_id', $soldItem->id)->get();
            $resetMessageCount = MessageRelation::where('destination_user_id', $user->id)->where('sold_item_id', $item->sold_item->id)->first();
            if (!$resetMessageCount == 0) {
                $resetMessageCount->update(['message_count' => 0]);
            }
            return view('sellChat', compact('user', 'item', 'buyer', 'otherItems', 'messages'));
        }
        return view('show', compact('item'));
    }

    public function getsell()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('sell', compact('categories', 'conditions'));
    }

    public function mypage(Request $request)
    {
        $user      = Auth::user();
        $profile   = Profile::where('user_id', $user->id)->first();
        $tab       = $request->query('tab');
        $averageReview = UserReview::where('user_id', $user->id)->avg('review');
        $messageCount = MessageRelation::where('destination_user_id', $user->id)->get();
        $roundAverage = round($averageReview, 0);
        if ($tab == 'buy') {
            $items = SoldItem::where('buyer_id', $user->id)->where('status', 'completed')->get()->map(function ($sold_item) {
                return $sold_item->item;
            });
        } elseif ($tab == 'progress') {
            $items = SoldItem::with('item')->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->whereIn('status', ['progress'])->withMax('messages', 'updated_at')->orderBy('messages_max_updated_at', 'desc')->get()->map(function ($sold_item) {
                return $sold_item->item;
            });

        } else {
            $items = Item::where('user_id', $user->id)->get();
        }
        return view('mypage', compact('user', 'profile', 'items', 'roundAverage', 'messageCount'));
    }

    /**
     * 出品処理
     * @param ExhibitionRequest $request リクエスト
     * @return view ビュー
     */
    public function postsell(ExhibitionRequest $request)
    {
        $new_item = Item::create([
            'user_id' => Auth::id(),
            'name'         => $request->input('name'),                               // 名前
            'brandname'    => $request->input('brandname'),                          // ブランド名
            'description'  => $request->input('description'),                        // 商品説明
            'price'        => $request->input('price'),                              // 値段
            'image'        => $request->file('image')->store('products', 'public'),  // 商品画像
            'condition_id' => $request->input('condition_id'),                       // 商品の状態
        ]);
        $item_categories   = $request->input('category', []);                          // 商品のカテゴリー
        $new_item->categories()->attach($item_categories);
        $items             = Item::get();
        return view('mypage', compact('items'));
    }

    /**
     * 購入処理
     * @param integer $item_id アイテムID
     * @return view ビュー
     */
    public function purchase($item_id, Request $request)
    {
        $item       = Item::with(['condition'])->find($item_id);
        $user       = Auth::user();
        $address    = SoldItem::where('buyer_id', $user->id)->first();
        $profile    = Profile::where('user_id', $user->id)->first();
        return view('purchase', compact('item', 'user', 'address', 'profile'));
    }

    public function soldOut(PurchaseRequest $request, $item_id)
    {
        $user                   = Auth::user();
        $item                   = Item::find($item_id);
        $item->purchase_method  = $request->input('purchase_method');
        $item->save();
        SoldItem::create([
            'buyer_id'  => $user->id,
            'seller_id'      => $item->user_id,
            'item_id'          => $item->id,
            'postcode'         => $request->input('postcode'),
            'address'          => $request->input('address'),
            'building'         => $request->input('building') ?? null,
        ]);
        return redirect('/');
    }

    public function review(request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::find($item_id);
        $seller = User::where('id', $item->user_id)->first();
        if ($item->sold_item->buyer_id == $user->id) {
            SoldItem::where('item_id', $item_id)->update([
                'status' => 'purchase_completed'
            ]);
            UserReview::create([
                'user_id' => $item->user_id,
                'review'  => $request->input('review')
            ]);
            Mail::to($seller->email)->send(new ReviewMail());
        } elseif ($item->sold_item->seller_id == $user->id) {
            SoldItem::where('item_id', $item_id)->update([
                'status' => 'completed'
            ]);
            UserReview::create([
                'user_id' => $item->sold_item->buyer_id,
                'review'  => $request->input('review')
            ]);
        }

        return redirect('/');
    }

}

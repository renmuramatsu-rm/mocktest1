@extends('layouts.authapp')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sellChat.css') }}">
@endsection

@section('content')
<div class="chat">
    <aside class="sidebar">
        <div class="sidebar-title">
            その他の取引
        </div>
        <div class="sidebar-item">
            @foreach ($otherItems as $otherItem)
            <form action="{{route('show', $otherItem->item_id)}}" method="get">
                <div class="item-name">
                    <button class="item-name__content">{{ $otherItem->item->name }}</button>
                </div>
            </form>
            @endforeach
        </div>
    </aside>

    <article class="content">
        <div class="article-title">
            <div class="title-item">
                <div class="title-img">
                    @if (isset($buyer->profile->image))
                    <img class="mypage-profile__img" src="{{ asset('storage/' . ($buyer->profile->image)) }}">
                    @else
                    <img class="mypage-profile__img" src="{{ asset('img/noimage.jpg') }}" alt="">
                    @endif
                </div>
                <div class="title-text">
                    <p>
                        「{{ $buyer->name }}」さんとの取引画面
                    </p>
                </div>
            </div>
        </div>
        <div class="item-detail">
            <div class="item-img">
                <img class="detail-item__img" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
            </div>
            <div class="item-info">
                <p class="item-name">
                    {{ $item->name }}
                </p>
                <p class="item-price">
                    {{ $item->price }}
                </p>
            </div>
        </div>
        <div class="chat-display">
            @foreach ($messages as $message)
            @if($message->user_id == $user->id)
            <div class="chat-send__user">
                <div class="chat-item">
                    <div class="chat-user__name">
                        <label for="user_name">{{ $user->name }}</label>
                        <input type="hidden" id="user_name" name="user" required>
                    </div>
                    <div class="chat-img">
                        <label for="user_name">
                            @if (isset($user->profile->image))
                            <img class="mypage-profile__img" src="{{ asset('storage/' . ($user->profile->image)) }}">
                            @else
                            <img class="mypage-profile__img" src="{{ asset('img/noimage.jpg') }}" alt="">
                            @endif
                        </label>
                    </div>
                </div>
                <div class="chat-message">
                    <form id="edit-{{ $message->id }}" class="chat-message__edit-form" action="{{ route('chat.update')}}" method="post">
                        @csrf
                        @method('PATCh')
                        <div class="chat-message__text">
                            <textarea type="text" class="send" name="text">{{ old('text', $message->text) }}</textarea>
                            <input type="hidden" name="id" value="{{ $message->id }}">
                        </div>
                        @if($message->img_url)
                        <div class=chat-message__img-item>
                            <img class="chat-user__img" src="{{ asset('storage/' . ($message->img_url ?? '')) }}">
                        </div>
                        @endif
                    </form>
                    <div class="chat-action">
                        <button class="chat-message__edit-btn" type="submit" form="edit-{{ $message->id }}">
                            編集
                        </button>
                        <form class="chat-message__delete" action="{{ route('chat.delete') }}" method="post">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" value="{{ $message->id }}">
                            <button class="chat-message__delete-btn" type="submit">
                                削除
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @elseif($message->user_id == $item->sold_item->buyer_id)
            <div class="chat-receive__user">
                <div class="chat-item">
                    <div class="chat-img">
                        <label for="user_name">
                            @if (isset($buyer->profile->image))
                            <img class="mypage-profile__img" src="{{ asset('storage/' . ($buyer->profile->image)) }}">
                            @else
                            <img class="mypage-profile__img" src="{{ asset('img/noimage.jpg') }}" alt="">
                            @endif
                        </label>
                    </div>
                    <div class="chat-user__name">
                        <label for="user_name">{{ $buyer->name }}</label>
                        <input type="hidden" id="user_name" name="user" required>
                    </div>
                </div>
                <div class="chat-message">
                    <div class="chat-message__text">
                        <textarea type="text" class="send" name="text" readonly>{{ old('text', $message->text) }}</textarea>
                    </div>
                    @if($message->img_url)
                    <div class=chat-message__img-item>
                        <img class="chat-user__img" src="{{ asset('storage/' . ($message->img_url ?? '')) }}">
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endforeach
        </div>
        <div class="chat-input">
            <div class="error-message">
                @error('text')
                {{ $message }}
                @enderror
            </div>
            <form class="chat-form" action="{{ route('chat.store', $item->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <label class="message-input">
                    <input class="chat-input__text" name="text" placeholder="取引メッセージを記入してください">
                </label>
                <label class="chat-img__btn">
                    画像を追加
                    <input class="chat-input__img" type="file" name="img_url" accept="image/png, image/jpeg">
                </label>
                <button type=" submit" class="chat-send__btn">
                    <img src="{{ asset('img/inputButton1.png') }}" alt="送信" class="chat-send__img">
                </button>
            </form>
        </div>
        <script>
            window.onload = () => {
                let chatBox = document.querySelector(".chat-display");
                chatBox.scrollTop = chatBox.scrollHeight;
            };
        </script>

        @if( $item->sold_item->status == 'purchase_completed')
        <div class="modal" id="{{$item->user_id}}">
            <a href="#" class="modal-overlay"></a>
            <div class="modal__inner">
                <div class="modal__content">
                    <form class="modal__detail-form" action="{{ route('review', $item->id)}}" method="post">
                        @csrf
                        <div class="modal-form__group">
                            <p class="modal-form__text-top">取引が完了しました。</p>
                        </div>
                        <div class="modal-form__review">
                            <p class="modal-form__text-inner">今回の取引相手はどうでしたか？</p>
                            <div class="form-review">
                                <input class="form-review__input" id="star5" name="review" type="radio" value="5">
                                <label class="form-review__label" for="star5"><i class="fa-solid fa-star"></i></label>

                                <input class="form-review__input" id="star4" name="review" type="radio" value="4">
                                <label class="form-review__label" for="star4"><i class="fa-solid fa-star"></i></label>

                                <input class="form-review__input" id="star3" name="review" type="radio" value="3">
                                <label class="form-review__label" for="star3"><i class="fa-solid fa-star"></i></label>

                                <input class="form-review__input" id="star2" name="review" type="radio" value="2">
                                <label class="form-review__label" for="star2"><i class="fa-solid fa-star"></i></label>

                                <input class="form-review__input" id="star1" name="review" type="radio" value="1">
                                <label class="form-review__label" for="star1"><i class="fa-solid fa-star"></i></label>
                            </div>
                        </div>
                        <div class="modal-btn">
                            <button class="modal-btn__item" type="submit">送信する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </article>
</div>
@endsection
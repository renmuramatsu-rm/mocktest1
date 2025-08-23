@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">
    <div class="mypage-profile__content">
        <div class="mypage-profile__user">
            <div class="mypage-profile__user-img">
                @if (isset($user->profile->image))
                <img class="mypage-profile__img" src="{{ asset('storage/' . ($profile->image)) }}">
                @else
                <img class="mypage-profile__img" src="{{ asset('img/noimage.jpg') }}" alt="">
                @endif
            </div>

            <div class="modal-form__review">
                <div class="mypage-profile__user-name">
                    <p>{{$user->name}}</p>
                </div>
                @if($roundAverage)
                <div class="form-review">
                    <div class="user-review">
                        @for ($i = 1; $i <=5; $i++)
                            <span class="fa-solid fa-star {{ $i <= $roundAverage ? 'checked' : '' }}"></span>
                            @endfor
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="mypage-profile__edit">
            <a href="{{ route('profile') }}" class="mypage-item__button-profile">プロフィールを編集</a>
        </div>
    </div>
    @php
    $activeTab = request('tab', 'sell');
    @endphp

    <ul class="mypage-list">
        <li class="mypage-list__item">
            <a href="/mypage?tab=sell" class="mypage-list__button-listing {{ $activeTab === 'sell' ? 'is-active' : '' }}">出品した商品</a>
        </li>
        <li class="mypage-list__item">
            <a href="/mypage?tab=buy" class="mypage-list__button-buy {{ $activeTab === 'buy' ? 'is-active' : '' }}">購入した商品</a>
        </li>
        <li class="mypage-list__item">
            <a href="/mypage?tab=progress" class="mypage-list__button-progress {{ $activeTab === 'progress' ? 'is-active' : '' }}">取引中の商品</a>
            @if($messageCount->isNotEmpty())
            @if($messageCount->sum('message_count') !== 0)
            <span class="totalMessageCount">{{ $messageCount->sum('message_count') }}</span>
            @endif
            @endif
        </li>
    </ul>
    <div class="mypage-item">
        @foreach ($items as $item)
        <div class="mypage-item_show">
            @if ($item->sold())
            <p class='sold-out'>
                Sold Out
            </p>
            @endif
            @php
            $soldId = $item->sold_item?->id;
            $count = $soldId
            ? (int) optional($messageCount->firstWhere('sold_item_id', $soldId))->message_count
            : 0;
            @endphp
            @if ($count > 0)
            <p class='messageCount'>
                {{ $count }}
            </p>
            @endif
            <div class="mypage-item__content">
                <a href="{{ route('show', $item->id) }}">
                    <img class="mypage-item__img" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                </a>
                <p class="mypage-item__name">{{ $item->name }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
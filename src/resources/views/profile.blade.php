@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-form__content">
    <div class="profile-form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form class="form" action="/mypage/profile" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <div class="form__group">
            <div class="form__group-content">
                <div class="form__group-user">
                    <div class=" mypage-profile__content-item">
                        @if (isset($profile->image))
                        <img class="mypage-profile__img" src="{{ asset('storage/' . ($profile->image)) }}" alt="">
                        @else
                        <img id="myImage" class="mypage-profile__img" src="{{ asset('img/noimage.jpg') }}" alt="">
                        @endif
                    </div>
                    <div class="form__input-text">
                        <label for="target" class="select-img">画像を選択する
                            <input id="target" type="file" name="image" accept="image/png,image/jpeg" class="hidden">
                        </label>
                    </div>
                </div>
                <div class="form__error">
                    @error('image')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label-item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="name" value="{{ old('name', $user->name) }}">
                </div>
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label-item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="postcode" value="{{ old('postcode', $profile->postcode ?? '') }}">
                </div>
                <div class="form__error">
                    @error('postcode')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label-item">住所</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
                </div>
                <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label-item">建物名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="building" value="{{ old('building', $profile->building ?? '') }}">
                </div>
                <div class="form__error">
                    @error('building')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
    <script>
        const target = document.getElementById('target');
        target.addEventListener('change', function(e) {
            const file = e.target.files[0]
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById("myImage");
                console.log(img.src);
                img.src = e.target.result;
                console.log(img.src);
            }
            reader.readAsDataURL(file);
        }, false);
    </script>

</div>



@endsection
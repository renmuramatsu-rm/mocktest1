@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-form">
    <div class="sell-form__heading">
        <h2>商品の出品</h2>
    </div>
    <form class="sell-form" action="/sell" method="post" enctype="multipart/form-data">
        @csrf
        <div class="sell-form__group">
            <div class="sell-form__content">
                <span class="sell-form__content-title">商品画像</span>
            </div>
            <div class="sell-form__content-item">
                <div class="sell__img">
                    <img class="appload__img" id="myImage">
                </div>
                <div class="sell-form__input-img">
                    <label for="target" class="select-img">
                        画像を選択する
                        <input id="target" type="file" name="image" class="hidden" accept="image/png,image/jpeg" />
                    </label>
                </div>
                <div class="form__error">
                    @error('image')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="sell-form__group">
            <div class="sell-form__title">
                <span class="sell-form__title-text">商品の詳細</span>
            </div>
            <div class="sell-form__group-content">
                <div class="sell-form__content">
                    <span class="sell-form__content-title">カテゴリー</span>
                </div>
                <div class="sell-form__category">
                    @foreach($categories as $category)
                    <label class="category__item-label"><input type="checkbox" class="category__item" name="category[]" value="{{ $category->id }}"><span class="category__item-box">{{ $category->category }}</span></label>
                    @endforeach
                </div>
                <div class="form__error">
                    @error('category')
                    {{ $message }}
                    @enderror
                </div>
                <div class="sell-form__content">
                    <span class="sell-form__content-title">商品の状態</span>
                </div>
                <div class="contact-form__select-inner">
                    <select class="contact-form__select" name="condition_id">
                        <option value="">選択してください</option>
                        @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id')==$condition->id ? 'selected' : '' }}>{{ $condition->condition }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form__error">
                    @error('condition_id')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="sell-form__group">
                <div class="sell-form__title">
                    <span class="sell-form__title-text">商品名と説明</span>
                </div>
                <div class="sell-form__group-content">
                    <div class="sell-form__content">
                        <span class="sell-form__content-title">商品名</span>
                    </div>
                    <div class="form__input-text">
                        <input type="text" name="name" value="{{ old('name') }}" />
                    </div>
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="sell-form__group-content">
                    <div class="sell-form__content">
                        <span class="sell-form__content-title">ブランド名</span>
                    </div>
                    <div class="form__input-text">
                        <input type="text" name="brandname" value="{{ old('brandname') }}" />
                    </div>
                </div>
                <div class="sell-form__group-content">
                    <div class="sell-form__content">
                        <span class="sell-form__content-title">商品の説明</span>
                    </div>
                    <div class="form__input-text">
                        <textarea name="description" rows="5">{{ old('description') }}</textarea>
                    </div>
                    <div class="form__error">
                        @error('description')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="sell-form__group-content">
                    <div class="sell-form__content">
                        <span class="sell-form__content-title">販売価格</span>
                    </div>
                    <div class="form__input-text">
                        <input type="text" name="price" value="{{ old('price') }}" />
                    </div>
                    <div class="form__error">
                        @error('price')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">出品する</button>
            </div>
        </div>
    </form>
    <script>
        const target = document.getElementById('target');
        const e = document.getElementById('appload');
        target.addEventListener('change', function(e) {
            const file = e.target.files[0]
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById("myImage")
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }, false);
    </script>
</div>

@endsection
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__logo">
            <a href="/"><img src="{{ asset('img/coachtech.svg') }}" alt="coachtech" class="coachtech__img"></a>
        </div>
        <form class="header_search" action="/" method="get">
            @csrf
            <input type="text" name="search" value="" placeholder="なにをお探しですか？">
            <button id="buttonElement" class="header_search--button">
                <img src="{{ asset('img/search_icon.jpeg') }}" alt="検索アイコン" style="height:100%;">
            </button>
        </form>
        <nav class="header__nav">
            <ul>
                @if(Auth::check())
                <li>
                    <form action="/logout" method="post">
                        @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
                <li><a href="/mypage">マイページ</a></li>
                @else
                <li><a href="/login">ログイン</a></li>
                <li><a href="/register">会員登録</a></li>
                @endif
                <a href="/sell">
                    <li class="header__btn">出品</li>
                </a>
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>
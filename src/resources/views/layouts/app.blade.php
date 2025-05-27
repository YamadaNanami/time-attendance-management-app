<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-wrap">
            <h1 class="page-logo">
                <a href="/" class="top-link">
                    <img src="{{ asset('/img/logo.svg') }}" alt="サイトのロゴ画像" class="logo-img">
                </a>
            </h1>
        </div>
        {{-- 一般ユーザーと管理者ユーザーでナビメニューの表示を切り替える --}}
        <nav class="header-nav">
            <ul>
                <li>
                    <a href="">勤怠</a>
                </li>
                <li>
                    <a href="">勤怠一覧</a>
                </li>
                <li>
                    <a href="">申請</a>
                </li>
                <li>
                    <a href="">ログアウト</a>
                </li>
            </ul>
            {{-- 管理者ユーザー用のナビメニュー
            <ul>
                <li>
                    <a href="">勤怠一覧</a>
                </li>
                <li>
                    <a href="">スタッフ一覧</a>
                </li>
                <li>
                    <a href="">申請一覧</a>
                </li>
                <li>
                    <a href="">ログアウト</a>
                </li>
            </ul> --}}
        </nav>
    </header>

    <main class="page-content">
        @yield('content')
    </main>
</body>

</html>
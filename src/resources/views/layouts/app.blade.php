<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common/ress.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/common.css') }}">
    @yield('css')
</head>

@php
    $isRoute = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('verification.notice') || request()->routeIs('admin.login');
@endphp
<body @if($isRoute) @else class="bg-default" @endif>
    <header class="header">
        <div class="header-wrap">
            <h1 class="page-logo">
                <a href="/" class="top-link">
                    <img src="{{ asset('/img/logo.svg') }}" alt="サイトのロゴ画像" class="logo-img">
                </a>
            </h1>
            @if(!Auth::check() || request()->routeIs('verification.notice'))
                {{-- 未ログイン時はナビメニューを表示させない --}}
            @elseif(Auth::user()->role == config('constants.ROLE.USER'))
                {{-- 一般ユーザー用のナビメニュー --}}
                <nav>
                    <ul class="nav-list-wrap">
                        <li class="nav-list">
                            <a href="{{ route('user.index') }}" class="nav-link">勤怠</a>
                        </li>
                        <li class="nav-list">
                            <a href="{{ route('user.attendance_list') }}" class="nav-link">勤怠一覧</a>
                        </li>
                        <li class="nav-list">
                            <a href="{{ route('application_list') }}" class="nav-link">申請</a>
                        </li>
                        <li class="nav-list">
                            <form action="/logout" method="post">
                                @csrf
                                <input type="hidden" name="role" value={{Auth::user()->role}}>
                                <button type="submit" class="nav-link">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                @elseif(Auth::user()->role == config('constants.ROLE.ADMIN'))
                    {{-- 管理者ユーザー用のナビメニュー --}}
                    <ul class="nav-list-wrap">
                        <li class="nav-list">
                            <a href="{{ route('admin.attendance_list') }}" class="nav-link">勤怠一覧</a>
                        </li>
                        <li class="nav-list">
                            <a href="{{ route('admin.staff_list') }}" class="nav-link">スタッフ一覧</a>
                        </li>
                        <li class="nav-list">
                            <a href="{{ route('application_list') }}" class="nav-link">申請一覧</a>
                        </li>
                        <li class="nav-list">
                        <form action="/logout" method="post">
                                @csrf
                                <input type="hidden" name="role" value={{Auth::user()->role}}>
                                <button type="submit" class="nav-link">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>
    </header>

    <main class="page-content">
        @yield('content')
    </main>
</body>

</html>
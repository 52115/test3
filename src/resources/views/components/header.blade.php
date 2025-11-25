<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ route('items.index') }}">CT COACHTECH</a>
        </div>
        <div class="search-bar">
            <form action="{{ request()->routeIs('items.mylist') ? route('items.mylist') : route('items.index') }}" method="GET">
                <input type="text" name="search" placeholder="なにをお探しですか?" value="{{ request('search') }}" style="width: 100%; padding: 0.5rem 1rem; border: none; border-radius: 4px;">
            </form>
        </div>
        <div class="header-nav">
            @auth
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="{{ route('profile.index') }}">マイページ</a>
                <a href="{{ route('sell.create') }}" class="btn-sell">出品</a>
            @else
                <a href="{{ route('login') }}">ログイン</a>
                <a href="{{ route('register') }}">会員登録</a>
            @endauth
        </div>
    </div>
</header>


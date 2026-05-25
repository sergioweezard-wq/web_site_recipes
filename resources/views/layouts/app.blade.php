<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Site Recipes' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<header>
    <nav class="navbar navbar-expand-sm navbar-light bg-white border-bottom box-shadow mb-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('home') }}">SiteRecipes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse d-sm-inline-flex justify-content-between" id="mainNavbar">
                <ul class="navbar-nav flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('home') }}">Головна</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('recipes.index') }}">Рецепти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('favorites.index') }}">Обране</a>
                    </li>
                    @auth
                        @if(auth()->user()->role === 'Admin')
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('admin.index') }}">Адмін-панель</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <form method="get" action="{{ route('recipes.index') }}" class="d-flex align-items-center gap-2 ms-3 me-3">
                    <input type="text" name="q" class="form-control nav-search-input" placeholder="Пошук рецептів..." value="{{ request('q') }}">
                    <button class="btn btn-outline-success" type="submit">Пошук</button>
                </form>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        <span class="text-muted small">{{ auth()->user()->email }}</span>
                        <form method="post" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                        </form>
                    @else
                        <a class="btn btn-sm btn-primary" href="/login">Login</a>
                        <a class="btn btn-sm btn-outline-primary" href="/register">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="container flex-grow-1">
    <main class="pb-3">
        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>

<footer class="border-top footer text-muted">
    <div class="container">
        &copy; 2026 - SiteRecipes
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

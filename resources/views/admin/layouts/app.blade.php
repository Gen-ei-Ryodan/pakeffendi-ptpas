<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
    <style>
        main input[type="text"],
        main input[type="email"],
        main input[type="password"],
        main input[type="number"],
        main input[type="tel"],
        main input[type="url"],
        main input[type="date"],
        main input[type="datetime-local"],
        main input[type="time"],
        main input[type="search"],
        main select,
        main textarea {
            border: 1px solid rgb(203 213 225);
            background: #fff;
        }

        main input[type="text"]:focus,
        main input[type="email"]:focus,
        main input[type="password"]:focus,
        main input[type="number"]:focus,
        main input[type="tel"]:focus,
        main input[type="url"]:focus,
        main input[type="date"]:focus,
        main input[type="datetime-local"]:focus,
        main input[type="time"]:focus,
        main input[type="search"]:focus,
        main select:focus,
        main textarea:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            border-color: rgb(14 165 233);
            box-shadow: 0 0 0 3px rgb(14 165 233 / 0.25);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="min-h-screen flex">
    @include('admin.layouts.sidebar')

    <div class="flex-1 flex flex-col">
        <header class="bg-white border-b border-slate-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button type="button" onclick="history.back()" class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-slate-100 border border-slate-200">
                        <span class="text-lg">&larr;</span>
                    </button>

                    <div>
                        <div class="text-sm text-slate-500">@yield('breadcrumb')</div>
                        <div class="text-lg font-semibold">@yield('header')</div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $name = auth()->user()?->name ?? 'Admin';
                        $parts = preg_split('/\s+/', trim($name));
                        $initials = strtoupper(collect($parts)->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                    @endphp

                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-semibold">
                            {{ $initials ?: 'A' }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                    </div>

                    <form method="post" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-100 text-sm">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-md rounded-lg bg-white shadow-sm border border-slate-200 p-6">
        <div class="text-xl font-semibold text-slate-900">Login</div>
        <div class="mt-1 text-sm text-slate-600">Gunakan username & webpassword staff</div>

        @if($errors->any())
            <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-900">
                <div class="text-sm font-medium">Login gagal</div>
                <div class="mt-1 text-xs font-mono break-all">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif

        <form method="POST" action="/login" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                    class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
            </div>
            <div>
                <label for="webpassword" class="block text-sm font-medium text-slate-700">Web Password</label>
                <input id="webpassword" name="webpassword" type="password" required
                    class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
            </div>
            <button type="submit"
                class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Login
            </button>
        </form>
    </div>
</body>
</html>


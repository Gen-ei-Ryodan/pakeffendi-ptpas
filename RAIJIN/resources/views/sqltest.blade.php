<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-slate-900 text-slate-100 px-4 py-6">
            <div class="text-lg font-semibold tracking-wide">Basic Dashboard</div>
            <nav class="mt-6 space-y-1">
                <a href="#dashboard" class="block rounded-md px-3 py-2 bg-slate-800 text-slate-100">Dashboard</a>
                <a href="#stock-list" class="block rounded-md px-3 py-2 text-slate-200 hover:bg-slate-800 hover:text-slate-100">Stock List</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <section id="dashboard" class="mb-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
                        <div class="text-sm text-slate-600">Data source: dbo.webstocklist</div>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <div class="text-sm text-slate-600">
                            {{ session('staff.staffname') ?? session('staff.UserName') }}
                        </div>
                        <div class="relative">
                            <input id="searchInput" type="text" placeholder="Search..." class="w-72 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                        </div>
                        <button id="refreshBtn" type="button" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Refresh
                        </button>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="rounded-md border px-4 py-3 {{ ($status ?? 'Failed') === 'Connected' ? 'border-green-200 bg-green-50 text-green-900' : 'border-red-200 bg-red-50 text-red-900' }}">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="font-semibold">Status: {{ $status ?? 'Failed' }}</div>
                            <div class="text-sm">
                                Rows: <span id="rowCount">{{ is_array($stocks ?? null) ? count($stocks) : 0 }}</span>
                            </div>
                        </div>
                        @if(!empty($error))
                            <div class="mt-2 break-all text-sm font-mono">{{ $error }}</div>
                        @endif
                    </div>
                </div>
            </section>

            <section id="stock-list">
                <div class="rounded-lg bg-white shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                        <div class="text-lg font-semibold text-slate-900">Stock List</div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                <span class="text-slate-600">Stock rendah (≤ {{ $lowStockThreshold ?? 5 }})</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                <span class="text-slate-600">Stock aman (&gt; {{ $lowStockThreshold ?? 5 }})</span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="stockTable" class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="0">Status</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="1">Stock ID</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="2">Name</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="3">Category</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="4">Warna</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="5">Size</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="6">Price</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="7">Qty</button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                                        <button type="button" class="sortBtn" data-col="8">Unit</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="stockTbody" class="divide-y divide-slate-100 bg-white">
                                @php
                                    $threshold = (int) ($lowStockThreshold ?? 5);
                                @endphp
                                @foreach(($stocks ?? []) as $row)
                                    @php
                                        $qty = (int) ($row->qty ?? 0);
                                        $isLow = $qty <= $threshold;
                                    @endphp
                                    <tr class="{{ $isLow ? 'bg-red-50' : 'bg-green-50' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700" data-type="text">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="inline-block h-2.5 w-2.5 rounded-full {{ $isLow ? 'bg-red-500' : 'bg-green-500' }}"></span>
                                                <span class="text-xs font-medium {{ $isLow ? 'text-red-700' : 'text-green-700' }}">{{ $isLow ? 'LOW' : 'OK' }}</span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700" data-type="text">{{ $row->stockid ?? '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-900 font-medium" data-type="text">{{ $row->stockname ?? '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700" data-type="text">{{ $row->categoryname ?? '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700" data-type="text">{{ $row->warna ?? '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700" data-type="text">{{ $row->prodsize ?? '' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700 text-right tabular-nums" data-type="number">{{ is_numeric($row->price ?? null) ? number_format((float) $row->price, 2, '.', ',') : ($row->price ?? '') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-900 text-right tabular-nums font-semibold" data-type="number">{{ $qty }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700" data-type="text">{{ $row->unitid ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-t border-slate-200 text-xs text-slate-500">
                        Sort: klik judul kolom. Search: filter real-time.
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const refreshBtn = document.getElementById('refreshBtn');
        const stockTable = document.getElementById('stockTable');
        const tbody = document.getElementById('stockTbody');
        const rowCountEl = document.getElementById('rowCount');

        function getCellValue(row, colIndex) {
            const cell = row.children[colIndex];
            if (!cell) return '';
            const type = cell.getAttribute('data-type') || 'text';
            const text = (cell.textContent || '').trim();
            if (type === 'number') {
                const n = parseFloat(text.replace(/,/g, ''));
                return Number.isFinite(n) ? n : 0;
            }
            return text.toLowerCase();
        }

        function updateVisibleCount() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const visible = rows.filter((r) => r.style.display !== 'none').length;
            rowCountEl.textContent = String(visible);
        }

        function applyFilter() {
            const q = (searchInput.value || '').toLowerCase().trim();
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (!q) {
                rows.forEach((r) => (r.style.display = ''));
                updateVisibleCount();
                return;
            }
            rows.forEach((r) => {
                const text = (r.textContent || '').toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
            updateVisibleCount();
        }

        let sortState = { col: 7, dir: 'desc' };

        function sortBy(colIndex) {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const dir = sortState.col === colIndex && sortState.dir === 'asc' ? 'desc' : 'asc';
            sortState = { col: colIndex, dir };

            rows.sort((a, b) => {
                const av = getCellValue(a, colIndex);
                const bv = getCellValue(b, colIndex);
                if (av < bv) return dir === 'asc' ? -1 : 1;
                if (av > bv) return dir === 'asc' ? 1 : -1;
                return 0;
            });

            rows.forEach((r) => tbody.appendChild(r));
        }

        searchInput.addEventListener('input', applyFilter);
        refreshBtn.addEventListener('click', () => window.location.reload());

        stockTable.querySelectorAll('.sortBtn').forEach((btn) => {
            btn.addEventListener('click', () => sortBy(parseInt(btn.getAttribute('data-col'), 10)));
        });

        sortBy(sortState.col);
        applyFilter();
    </script>
</body>
</html>

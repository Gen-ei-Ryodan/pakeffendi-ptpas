<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;

class SqlTestController extends Controller
{
    public function index()
    {
        $stocks = [];
        $error = null;
        $status = 'Failed';
        $lowStockThreshold = 5;

        try {
            $opensslConf = env('OPENSSL_CONF') ?: base_path('openssl_custom.cnf');
            if (! getenv('OPENSSL_CONF') && is_string($opensslConf) && $opensslConf !== '' && is_file($opensslConf)) {
                putenv("OPENSSL_CONF={$opensslConf}");
            }

            DB::connection('sqlsrv')->getPdo();
            $status = 'Connected';

            $stocks = DB::connection('sqlsrv')->select(
                '
                SELECT TOP (500)
                    stockid,
                    stockname,
                    categoryname,
                    stocktypeid,
                    warna,
                    prodsize,
                    price,
                    qty,
                    unitid
                FROM dbo.webstocklist
                ORDER BY stockname
                '
            );
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        return view('sqltest', compact('status', 'error', 'stocks', 'lowStockThreshold'));
    }
}

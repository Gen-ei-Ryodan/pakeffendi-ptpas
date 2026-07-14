<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAuthController extends Controller
{
    public function show()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'webpassword' => ['required', 'string'],
        ]);

        try {
            $staff = DB::connection('sqlsrv')->selectOne(
                '
                SELECT TOP (1)
                    StaffID,
                    staffname,
                    UserName,
                    IsAdmin
                FROM dbo.Staff
                WHERE UserName = ?
                  AND webpassword = ?
                ',
                [$validated['username'], $validated['webpassword']]
            );

            if (! $staff) {
                return back()
                    ->withInput($request->only(['username']))
                    ->withErrors(['username' => 'Username / password salah.']);
            }

            $request->session()->regenerate();
            $request->session()->put('staff', [
                'StaffID' => $staff->StaffID ?? null,
                'staffname' => $staff->staffname ?? null,
                'UserName' => $staff->UserName ?? null,
                'IsAdmin' => $staff->IsAdmin ?? null,
            ]);

            return redirect()->intended('/');
        } catch (Exception $e) {
            return back()
                ->withInput($request->only(['username']))
                ->withErrors(['username' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('staff');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

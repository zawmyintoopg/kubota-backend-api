<?php

// app/Http/Controllers/SettingController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function printer()
    {
        $setting = DB::table('settings')->first();
        return view('settings.printer', compact('setting'));
    }

    public function savePrinter(Request $request)
    {
        $request->validate([
            'printer_type' => 'required|in:80mm,A4'
        ]);

        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            ['printer_type' => $request->printer_type]
        );

        return back()->with('success','Printer setting updated successfully');
    }
}


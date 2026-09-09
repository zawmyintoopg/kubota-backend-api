<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // Show shifts / current shift info
    public function index(Request $request)
    {
        $currentShift = Shift::where('user_id', Auth::id())
                            ->where('status','open')
                            ->first();

        if ($currentShift) {
            $sales = \App\Models\Sale::whereDate('sale_date', $currentShift->transdate)
                                    ->where('user_id', Auth::id())
                                    ->get();

            $currentShift->cash_sales = $sales->where('payment_method_id', 1)->sum('paid_amount');
            $currentShift->card_sales = $sales->where('payment_method_id', 2)->sum('paid_amount');
            $currentShift->total_sales = $currentShift->cash_sales + $currentShift->card_sales;
            $currentShift->difference = $currentShift->total_sales - $currentShift->opening_cash;
        }

        if($request->ajax()){
            return response()->json([
                'currentShift' => $currentShift
            ]);
        }

        return view('shifts.index', compact('currentShift'));
    }


    // Start shift
    public function store(Request $request)
    {
        $currentShift = Shift::where('user_id', Auth::id())
                             ->where('status','open')
                             ->first();

        if($currentShift){
            return redirect()->back()->with('error', 'You already have an open shift.');
        }

        Shift::create([
            'user_id' => Auth::id(),
            'transdate' => today(),
            'open_time' => now()->format('H:i:s'),
            'opening_cash' => $request->opening_cash ?? 0,
            'status' => 'open',
            'remark' => $request->remark ?? null
        ]);

        return redirect()->back()->with('success','Shift started successfully.');
    }

    // Close shift
    public function closeShift(Request $request, $id)
    {
        $shift = Shift::where('id',$id)
                      ->where('user_id', Auth::id())
                      ->where('status','open')
                      ->first();

        if(!$shift){
            return redirect()->back()->with('error','No open shift found.');
        }

        $shift->update([
            'close_time' => now()->format('H:i:s'),
            'cash_sales' => $request->cash_sales ?? 0,
            'card_sales' => $request->card_sales ?? 0,
            'total_sales' => ($request->cash_sales ?? 0) + ($request->card_sales ?? 0),
            'difference' => ($request->closing_cash ?? 0) - $shift->opening_cash - ($request->cash_sales ?? 0),
            'status' => 'closed',
            'remark' => $request->remark ?? null
        ]);

        return redirect()->back()->with('success','Shift closed successfully.');
    }
}

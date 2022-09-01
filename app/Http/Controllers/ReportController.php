<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Resources\reportsResource;
use App\Http\Resources\orderShowResource;
use App\Models\OrderItem;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validated = request()->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        if (Order::filterByDate($validated['start_date'], $validated['end_date'])
            ->orderBy('created_at')->exists()
        ) {
            $order = Order::where('is_complete', true)
                ->filterByDate($validated['start_date'], $validated['end_date'])
                ->search(request('search'))
                ->orderBy('created_at')
                ->paginate(15);

            return  orderShowResource::collection($order);
        } else {
            return response()->json(['message' => 'No data matching your criteria'], 200);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemsReport()
    {
        $validated = request()->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        if (OrderItem::filterByDate($validated['start_date'], $validated['end_date'])
        ->orderBy('created_at')->exists()) {
            $order = Order::where('is_complete', true)
                ->filterByDate($validated['start_date'], $validated['end_date'])
                ->search(request('search'))
                ->orderBy('created_at')
                ->paginate(15);

            return  orderShowResource::collection($order);
        } else {
            return response()->json(['message' => 'No data matching your criteria'], 200);
        }
    }
}

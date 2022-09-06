<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Resources\reportsResource;
use App\Http\Resources\orderShowResource;
use App\Http\Resources\orderItemsResource;
use App\Http\Resources\reportItemsResource;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;

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

            return  reportsResource::collection($order);
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
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if (Report::filterByDate($validated['start_date'], $validated['end_date'])
            ->orderBy('created_at')->exists()
        ) {
            $report = Report::whereHas('order', function ($query) {
                return $query->where('is_complete', true);
            })->filterByDate($validated['start_date'], $validated['end_date'])
                ->search(request('search'))
                ->orderBy('created_at')->get()->loadMissing(['reportItems']);
            // return $order;
            return  reportItemsResource::collection($report);
        } else {
            return response()->json(['message' => 'No data matching your criteria'], 200);
        }
    }
}

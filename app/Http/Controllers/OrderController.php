<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Jobs\SendSms;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $validdata = $request->validated();

        //save new order to database
        // if ($validdata['menu_id']=) {
        // }
        DB::transaction(function () use ($validdata) {
            Order::create([
                'menu_id' => $validdata['menu_id'],
                'phone' => $validdata['phone'],
                'location' => $validdata['location']
            ]);
        });
        //retrieve created order
        $menu = Menu::select('title')->where('id', $validdata['menu_id'])->first();

        // send sms to restaurant and buyer
        $textrestaurant = "order of: {$menu} has been made by {$validdata['phone']}. from: {$validdata['location']}";
        SendSms::dispatch($textrestaurant, 255620170041);

        // Send code to user mobile
        $textbuyer = "{$menu['title']} is your selected food for today.";
        SendSms::dispatch($textbuyer, $validdata['phone']);


        // return response to client
        return response()->json([
            'response' =>
            [
                'order' => 'Order of: ' . $menu['title'] . ' has been made',
                'message' => 'You will receive an sms for confirmation.',


            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}

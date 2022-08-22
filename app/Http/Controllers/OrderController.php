<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Jobs\SendSms;
use App\Models\Order;
use App\Models\Report;
use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\SendOrderRequest;
use App\Http\Resources\reportsResource;
use Illuminate\Validation\Rules\Exists;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\orderShowResource;
use App\Http\Resources\orderItemsResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::orderby('created_at', 'desc')->paginate(15);

        return $orders;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function employeeOrders(Order $order)
    {
        $order->loadMissing(['orderItems']);

        return (new orderShowResource($order))->response();
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($order)
    {
        $items = OrderItem::where('order_id', $order)->orderby('created_at', 'desc')->paginate(15);
        $items->loadMissing(['employeeItems']);
        return orderItemsResource::collection($items);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        // DB::transaction(function () use ($validated) {
        $order = Order::create([
                'is_complete' => false
        ]);

        // print data_get($validated, '*.qty');
        //Loop menu items and insert into the database
        foreach ($validated['items'] as $item) {

            $qty = data_get($item, 'menu.*.qty');
            $price = Menu::select('price')->where('id', data_get($item, 'menu.*.menu_id'))->first();
//amount not exact it only takes the first menu.
            $items = $order->orderItems()->create([
                'employee_id' => $item['employee_id'],
                'amount' => (int) $price->price * (int) $qty,
            ]);

            foreach ($item['menu'] as $i) {
                $items->employeeItems()->create([
                    'menu_id' => $i['menu_id'],
                    'quantity' => $i['qty']
                ]);
            }

            // return $price =  Menu::where('id', $res)->first();

            //     $price = Menu::select('price')->where('id', data_get($item, 'menu.*.menu_id');)->first();
            //     // echo $total= $price->price;
        }
        // });
        // return response to client
        return response()->json([
            'response' => ['order' => 'Order created successfully']
        ]);
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
        $validated = $request->validated();
        if (!$order->is_placed == true) {
            $order = DB::transaction(function () use ($validated, $order) {
                // Add order items
                if (!empty($validated['add_item'])) {
                    foreach ($validated['add_item'] as $data) {
                        $order->orderItems()->create([
                            'menu_id' => $data['menu_id'],
                            'employee_id' => $data['employee_id'],
                        ]);
                    }
                }
                // Delete order items
                if (!empty($validated['delete_item'])) {
                    foreach ($validated['delete_item'] as $id) {
                        OrderItem::where('id', $id)->delete();
                    }
                }
            });
            // return response to client
            return response()->json([
                'response' => ['order' => 'Order updated successfully']
            ]);
        } else {
            return response()->json(['error' => 'Updating a completed order is not allowed'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function send(SendOrderRequest $request)
    {
        $validated = $request->validated();

        $db = DB::transaction(function () use ($validated) {
            // change order status in db
            $order = Order::find($validated['order_id']);
            $order->is_placed = true;
            $order->save();

            // create report
            if (Order::where([
                'id' => $validated['order_id'],
                'is_placed' => true
            ])->first()) {
                // retrieve orderItems of this order
                $items = OrderItem::where([
                    'order_id' => $validated['order_id']
                ])->with(['menu', 'employee'])->get();

                // loop through items to store into report table;
                foreach ($items as $item) {
                    Report::create([
                          'order_number' => $order->order_number,
                          'employee' => $item->employee->name,
                          'menu' => $item->menu->title,
                          'amount' => $item->menu->price
                      ]);
                }

        //         // send sms to restaurant and buyer
        //         // $textrestaurant = "Order# {$order['order_number']}";
        //         // return response()->json(["order" => $textrestaurant]);

        //         // SendSms::dispatch($textrestaurant, 255620170041);
        //         // return response()->json(["order" => $textrestaurant]);
            }
        });



        // send sms/email to restaurant
        // return response to client
        return response()->json([
            'response' => ['order' => 'Order sent successfully']
        ]);
    }
}

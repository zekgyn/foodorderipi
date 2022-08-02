<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Jobs\SendSms;
use App\Models\Order;
use App\Models\Report;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Validated;
use App\Http\Requests\SendOrderRequest;
use Illuminate\Validation\Rules\Exists;
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
        $orders = Order::orderby('created_at', 'desc')->paginate(15);

        return $orders;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($order)
    {
        $result = Order::select('id', 'order_number', 'created_at')->where('id', $order)
        ->with('orderItems:id,employee_id,menu_id,order_id')
        ->orderBy('created_at')->get();
        // $order->with('orderItems:id,employee_id,menu_id,order_id')->get();
        // $result = $order->with('orderItems')->get();

    //    $order->with(['orderItems:id' => function ($q) use ($order) {
    //         $q->wherePivot('menu_id', '=', $order);
    //     }])->first();

        return $result;
        // return response()->json($result);
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

        DB::transaction(function () use ($validated) {
            $order = Order::create([
                'is_placed' => false
            ]);
            //Loop menu items and insert into the database
            foreach ($validated['menus'] as $menuitem) {
                $menu = Menu::select('id', 'price', 'title')
                    ->where('id', $menuitem['menu_id'])
                    ->first();
                // Create order items for new order
                $order->orderItems()->create([
                    'menu_id' => $menu['id'],
                    'employee_id' => $menuitem['employee_id'],
                    // 'image' => $menu['image'],
                    // 'price' => $menu['price']
                ]);
            }
        });
        // return response to client
        return response()->json([
            'response' => ['order' => 'Order created successfully']
        ]);
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
        //change order status in db
        $send = DB::transaction(function () use ($validated) {
            $order = Order::find($validated['order_id']);
            $order->is_placed = true;
            $order->save();


        //retrieve created order
        if (Order::where([
            'id' => $validated['order_id'],
            'is_placed' => true
        ])->first()) {
            // retrieve order info
            $order = Order::where([
                'id' => $validated['order_id'],
                'is_placed' => true
            ])->first();
            // send sms to restaurant and buyer
            $textrestaurant = "Order# {$order['order_number']}";
            // return response()->json(["order" => $textrestaurant]);
            $items = $order->load('orderItems:id,order_id,menu_id');
            foreach ($items['orderItems'] as $item) {

                // return response()->json(["item" => $item]);
            }
            // send sms to restaurant

            // SendSms::dispatch($textrestaurant, 255620170041);
            // return response()->json(["order" => $textrestaurant]);
        }
        });
        // return response to client
        return response()->json([
            'response' => ['order' => 'Order sent successfully']
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
                        $menu = Menu::where('id', $data['menu_id'])
                            ->first();

                        $order->orderItems()->create([
                                'menu_id' => $menu['id'],
                                'employee_id' => $data['employee_id'],
                                // 'title' => $menu['title'],
                                // 'image' => $menu['image'],
                                // 'price' => $menu['price']
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
            return response()->json(['error'=>'Updating a complete order is not allowed'], 422);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        if (OrderItem::filterByDate(request('start_date'),request('end_date'))
                ->orderBy('created_at')->exists()) {
            $report = Order::select('id','order_number','created_at')->filterByDate(request('start_date'), request('end_date'))
                ->with('orderItems:id,employee_id,menu_id,order_id')
                ->orderBy('created_at')->get();

            return response()->json($report);

        }
        else {
            return response()->json([],404);
        }

    }
}

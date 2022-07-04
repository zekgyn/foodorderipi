<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Jobs\SendSms;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SendOrderRequest;
use Illuminate\Validation\Rules\Exists;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\OrderItem;
use Illuminate\Auth\Events\Validated;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where([
            'is_placed' => false
        ])->paginate(10);

        return $orders;
    }
    public function indexClosed()
    {
        $orders = Order::where([
            'is_placed' => true
        ])->paginate(10);

        return $orders;
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
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            $order = Order::create([
                'is_placed' => false
            ]);
            //Loop menu items and insert into the database
            foreach ($validated['menus'] as $menuitem) {

                $menu = Menu::select('id', 'price', 'image', 'title')
                    ->where('id', $menuitem['menu_id'])
                    ->first();
                // Create order items for new order
                $order->orderItems()->create([
                    'menu_id' => $menu['id'],
                    'title' => $menu['title'],
                    'name' => $menuitem['name'],
                    'image' => $menu['image'],
                    'price' => $menu['price']
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

        //save new order to database

        $send = DB::transaction(function () use ($validated) {

            $order = Order::find($validated['order_id']);
            $order->is_placed = true;
            $order->save();
        });
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
            // SendSms::dispatch($textrestaurant, 255620170041);

            // // Send code to user mobile
            // $textbuyer = "{$menu['title']} is your selected food for today.";
            // SendSms::dispatch($textbuyer, $validated['phone']);
        }




        // return response to client
        return response()->json([
            'response' => ['order' => 'Order sent successfully']
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
        if ($order->is_placed == false) {
            $result = $order->load('orderItems:id,order_id,title,name');
            return response($result);
        } else {
            return response()->json(["message" => "Order already closed"]);
        }
    }

    public function closedShow(Order $order)
    {

        if ($order->is_placed == true) {
            $result = $order->load('orderItems:id,order_id,title,name');
            return response()->json($result);
        } else {
            return response()->json(["message" => "Order is still open"]);
        }
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

        $order = DB::transaction(function () use ($validated, $order) {
            // Add order items
            if (!empty($validated['add_menus'])) {
                foreach ($validated['add_menus'] as $data) {
                    $menu = Menu::select('id', 'price', 'image', 'title')
                        ->where('id', $data['menu_id'])
                        ->first();
                    $order->orderItems()->create([
                        'menu_id' => $menu['id'],
                        'title' => $menu['title'],
                        'name' => $data['name'],
                        'image' => $menu['image'],
                        'price' => $menu['price']
                    ]);
                }
            }
            // Delete order items
            if (!empty($validated['delete_menus'])) {
                foreach ($validated['delete_menus'] as $id) {
                    OrderItem::where('id', $id)->delete();
                }
            }
            return $order;
        });


        // return response to client
        return response()->json([
            'response' => ['order' => 'Order updated successfully']
        ]);
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

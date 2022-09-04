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
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\orderItemsResource;
use App\Models\EmployeeMenuItems;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::orderby('created_at', 'desc')
            ->search(request('search'))
            ->paginate(15);

        return orderShowResource::collection($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->loadMissing('orderItems.employeeItems');
        return (new orderShowResource($order))->response();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employeeOrders($order)
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

        DB::transaction(function () use ($validated) {
            // sum of all items for order
            $total = 0;
            foreach ($validated['items'] as $item) {
                foreach ($item['menu'] as $i) {
                    $price = Menu::select('price')->where('id', $i['id'])->first();
                    $subTotal = $price->price * $i['qty'];
                    $total += $subTotal;
                }
            }
            // create order
            $order = Order::create([
                'is_complete' => false,
                'total' => $total
            ]);
            //Loop menu items and insert into the database
            foreach ($validated['items'] as $item) {
                $itemTotal = 0;
                foreach ($item['menu'] as $i) {
                    $price = Menu::select('price')->where('id', $i['id'])->first();
                    $itemSubTotal = $price->price * $i['qty'];
                    $itemTotal += $itemSubTotal;
                }

                $items = $order->orderItems()->create([
                    'employee_id' => $item['employee_id'],
                    'subtotal' =>  $itemTotal,
                ]);

                foreach ($item['menu'] as $menu) {
                    $items->employeeItems()->create([
                        'menu_id' => $menu['id'],
                        'quantity' => $menu['qty']
                    ]);
                }
            }
        });
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
        if (!$order->is_complete == true) {
            $order = DB::transaction(function () use ($validated, $order) {
                // // Add order items
                if (!empty($validated['add_items'])) {
                    //     // new sum of all items for order
                    $total = $order->total;
                    foreach ($validated['add_items'] as $item) {
                        foreach ($item['menu'] as $i) {
                            $price = Menu::select('price')->where('id', $i['id'])->first();
                            $subTotal = $price->price * $i['qty'];
                            $total += $subTotal;
                        }
                    }
                    //     // update order price
                    $order->update([
                        'total' => $total
                    ]);
                    //     // add new items
                    foreach ($validated['add_items'] as $data) {
                        $total = 0;
                        foreach ($data['menu'] as $i) {
                            $price = Menu::select('price')->where('id', $i['id'])->first();
                            $subTotal = $price->price * $i['qty'];
                            $total += $subTotal;
                        }

                        $items = $order->orderItems()->create([
                            'employee_id' => $data['employee_id'],
                            'subtotal' => $total,
                        ]);
                        foreach ($data['menu'] as $item) {
                            $items->employeeItems()->create([
                                'menu_id' => $item['id'],
                                'quantity' => $item['qty']
                            ]);
                        }
                    }
                }
                // Delete order items
                if (!empty($validated['delete_items'])) {
                    $total = $order->total;
                    // new sum of all items for order
                    foreach ($validated['delete_items'] as $id) {
                        $item = $order->orderItems()->select('subtotal')->where('id', $id)->first();
                        $total -= $item->subtotal;
                        $order->update([
                            'total' => $total
                        ]);
                        $order->orderItems()->where('id', $id)->delete();
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
     * Update the specified resource in storage.
     *
     * @param  \App\Models\OrderItem  $item
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateItem(Request $request, OrderItem $item)
    {
        $validated = Validator::make($request->all(), [
            'add_menu' => 'array',
            'add_menu.*.id' => [
                'required', function ($attribute, $value, $fail) {
                    if (!Menu::where([
                        ['id', '=', $value]
                    ])->exists() || !Menu::where([
                        ['id', '=', $value],
                        ['is_active', '=', true]
                    ])->exists()) {
                        return $fail("{$attribute} does not exist in the menu");
                    }
                }
            ],
            'add_menu.*.qty' => 'bail|required|numeric|min:1',
            'delete_menu' => 'present|nullable|array',
            'delete_menu.*' => 'required|distinct|exists:employee_menu_items,id'
        ], [
            'delete_menu.*.exists' => 'Menu item does not exist',
        ])->validate();

        if ($item->order()->where([
            'id' => $item->order_id,
            'is_complete' => false
        ])->exists()) {
            DB::transaction(function () use ($validated, $item) {
            // Add employee items
            if (!empty($validated['add_menu'])) {
                $order = $item->order()->where('id', $item->order_id)->first();
                $orderTotal= $order->total;
                $itemTotal = $item->subtotal;
                foreach ($validated['add_menu'] as $i) {
                    $price = Menu::select('price')->where('id', $i['id'])->first();
                    $subTotal = $price->price * $i['qty'];
                    $itemTotal += $subTotal;
                    $orderTotal += $subTotal;
                }
                $item->update([
                    'subtotal' => $itemTotal,
                ]);
                $order->update([
                    'total' => $orderTotal
                ]);

                foreach ($validated['add_menu'] as $i) {
                    $item->employeeItems()->create([
                        'menu_id' => $i['id'],
                        'quantity' => $i['qty']
                    ]);
                }
            }
            // Delete employee items
            if (!empty($validated['delete_menu'])) {
                $order = $item->order()->where('id', $item->order_id)->first();
                $orderTotal = $order->total;
                $itemTotal = $item->subtotal;
                foreach ($validated['delete_menu'] as $id) {

                    $orderItem = $item->employeeItems()->where('id', $id)->first();
                    $price = Menu::select('price')->where('id', $orderItem->menu_id)->first();
                    $itemTotal -= $price->price;
                    $orderTotal -= $price->price;
                    $item->update([
                        'subtotal' => $itemTotal,
                    ]);
                    $order->update([
                        'total' => $orderTotal
                    ]);
                    $orderItem->where('id', $id)->delete();
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
            $order->is_complete = true;
            $order->save();

            // create report
            //     if (Order::where([
            //         'id' => $validated['order_id'],
            //         'is_complete' => true
            //     ])->first()) {
            //         // retrieve orderItems of this order
            //         $items = OrderItem::where([
            //             'order_id' => $validated['order_id']
            //         ])->with(['menu', 'employee'])->get();

            //         // loop through items to store into report table;
            //         foreach ($items as $item) {
            //             Report::create([
            //                 'order_number' => $order->order_number,
            //                 'employee' => $item->employee->name,
            //                 'menu' => $item->menu->title,
            //                 'amount' => $item->menu->price
            //             ]);
            //         }

            //         //         // send sms to restaurant and buyer
            //         //         // $textrestaurant = "Order# {$order['order_number']}";
            //         //         // return response()->json(["order" => $textrestaurant]);

            //         //         // SendSms::dispatch($textrestaurant, 255620170041);
            //         //         // return response()->json(["order" => $textrestaurant]);
            //     }
        });



        // send sms/email to restaurant
        // return response to client
        return response()->json([
            'response' => ['message' => 'Order sent successfully']
        ]);
    }
}

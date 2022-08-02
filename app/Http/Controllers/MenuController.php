<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = Menu::select('id', 'title','price')->orderby('created_at', 'desc')->paginate(15);

        return response()->json(['data' => $menu]);
    }
    public function indexall()
    {
        $menu = Menu::select('id', 'title','price')->orderby('created_at','desc')->get();

        return response()->json(['data' => $menu]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMenuRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMenuRequest $request)
    {
        $validdata = $request->validated();

        // create menu
        $product = Menu::create([
            'title' => strtolower($validdata['title']),
            // 'image' => $validdata['image'],
            'price' => $validdata['price'],
        ]);

        // return response to client
        return response()->json([
            'response' => ['menu' => $validdata['title'] . ' has been added to menu']
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMenuRequest  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $data = Validator::make($request->all(), [
            'title' => [
                'required', 'string',
                function ($attribute, $value, $fail) use ($menu) {
                    if (Menu::where([
                        ['id', '!=', $menu->id],
                        ['title', '=', strtolower($value)]
                    ])->exists()) {
                        return $fail("{$attribute} already exists");
                    }
                }
            ],
            'price' => 'required|regex:/^\d{1,16}+(\.\d{1,2})?$/'
            // 'image' => 'present|nullable'
        ])->validate();

        if (Menu::where([
            ['id', '=', $menu->id]
        ])->exists()){
            $menu->update([
                'title' => strtolower($data['title']),
                'price' => $data['price'],
                // 'image' => $data['image'],
            ]);
            return response()->json([
                'response' => 'menu has been updated'
            ]);
        }else{
            return response()->json([
                'response' => 'This menu no longer exists'
            ]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        if(Menu::where('id', $menu->id)
        ->exists()){
            $menu->destroy($menu->id);

            return response()->json([
                'response' => 'Menu has been deleted successfully'
            ]);
        }
            else {
            return response()->json([
                'response' => 'Menu does not exist'
            ]);
            }

    }
}

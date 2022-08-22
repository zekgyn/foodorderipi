<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\menuResource;
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
        $menu = Menu::select('id', 'title', 'price', 'is_active')->orderby('created_at', 'desc')->paginate(15);

        return menuResource::collection($menu);
    }
    public function indexall()
    {
        $menu = Menu::where('is_active', true)->select('id', 'title', 'price')->orderby('created_at', 'desc')->get();

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
        $menu = Menu::create([
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
        ],[
            'title.required' => 'Unique title is required.',
            'price.required' => 'The :attribute field is required.',
            ])->validate();

        if (Menu::where([
            ['id', '=', $menu->id]
        ])->exists()) {
            $menu->update([
                'title' => strtolower($data['title']),
                'price' => $data['price'],
                // 'image' => $data['image'],
            ]);
            return response()->json([
                'response' => 'menu has been updated'
            ]);
        } else {
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
    public function menuStatus(Request $request, Menu $menu)
    {
        $validated = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ])->validate();

        // $menu->fill($request->only(['is_active']));
        $menu->is_active = $validated['is_active'];

        if ($menu->isClean('is_active')) {
            return response()->json(['message' => 'You need to specify different value'], 422);
        }

        $menu->save();
        return response()->json(['message' => 'Menu Status updated']);
    }
}

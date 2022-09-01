<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\employeeResource;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = Employee::select('id', 'name', 'phone', 'is_active')->search(request('search'))->orderby('created_at', 'desc')->paginate(15);

        return employeeResource::collection($employee);
    }
    public function indexall()
    {
        $employee = Employee::select('id', 'name', 'phone')->where('is_active', true)->search(request('search'))->orderby('created_at', 'desc')->get();

        return response()->json(['data' => $employee]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data= $request->validated();

        Employee::create([
            'name' => strtolower($data['name']),
            'phone' => $data['phone'],
        ]);
        return response()->json([
            'response' => 'Employee  has been created successfully'
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeRequest  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $data = Validator::make($request->all(), [
            'phone' => [
                'required', 'integer','digits:12',
                function ($attribute, $value, $fail) use ($employee) {
                    if (Employee::where([
                        ['id', '!=', $employee->id],
                        ['phone', '=', $value]
                    ])->exists()) {
                        return $fail("{$attribute} number already exists");
                    }
                }
            ],
            'name' => 'required|string'
            // 'image' => 'present|nullable'
        ])->validate();

        if (Employee::where([
            ['id', '=', $employee->id]
        ])->exists()) {
            $employee->update([
                'name' => strtolower($data['name']),
                'phone' => $data['phone']
            ]);
            return response()->json([
                'response' => 'employee has been updated'
            ]);
        } else {
            return response()->json([
                'response' => 'This employee no longer exists'
            ]);
        }
    }

    public function employeeStatus(Request $request, Employee $employee)
    {
        $validated = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ])->validate();


        $employee->is_active = $validated['is_active'];

        if ($employee->isClean('is_active')) {
            return response()->json(['message' => 'You need to specify different value'], 422);
        }

        $employee->save();
        return response()->json(['message' => 'Employee Status updated']);
    }
}

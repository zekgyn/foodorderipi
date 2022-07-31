<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = Employee::select('id', 'name', 'phone')->orderby('created_at', 'desc')->paginate(15);

        return response()->json(['data' => $employee]);
    }
    public function indexall()
    {
        $employee = Employee::select('id','name', 'phone')->orderby('created_at', 'desc')->get();

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
            'name' => 'required|string',
            'phone' =>'required|integer|unique:employees|digits:12',
            // [
            //     'required', 'integer', 'unique:employees', 'digits:12',
            //     function ($attribute, $value, $fail) use ($employee) {
            //         if (Employee::where([
            //             ['id', '!=', $employee->id],
            //             ['phone', '=', $value]
            //         ])->exists()) {
            //             return $fail("{$attribute} already exists");
            //         }
            //     }
            // ]
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        if (Employee::where('id', $employee->id)
            ->exists()
        ) {
            $employee->destroy($employee->id);

            return response()->json([
                'response' => 'Employee has been deleted successfully'
            ]);
        } else {
            return response()->json([
                'response' => 'Employee does not exist'
            ]);
        }

    }
}

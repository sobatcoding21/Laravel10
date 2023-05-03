<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $table = Customer::orderBy('created_at', 'DESC')->get();
            return response()->json(['data' => $table]);
        }
        return view('customer.index');
    }

    public function show($customer)
    {
        $data = Customer::findOrFail($customer);
        return response()->json($data);
    }

    public function create(Request $request)
    {
        return view('customer.form');
    }

    public function edit($customer, Request $request)
    {
        $data = Customer::findOrFail($customer);
        return view('customer.form', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
        ],
        [
            'required'  => ':attribute harus diisi',
        ]);
 
        if ($validator->fails()) {

            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 422]);
        }

        $update =  new Customer;
        $update->name = $request->get('name');
        $update->address = $request->get('address');
        $update->save();

        if($request->file('photo')){
            $file= $request->file('photo');
            if( in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']) ) //validate image
            {
                $filename= date('YmdHis')."_". Str::slug($update->name) . "." . $file->getClientOriginalExtension();
                $file->move(public_path('avatars'), $filename);

                Customer::where('id', $update->id)->update([ 'avatar' => "avatars/". $filename ]);
            }

        }

        return response()->json(['success' => true, 'message' => 'Data pelanggan berhasil ditambahkan']);
    }

    public function update($customer, Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
        ],
        [
            'required'  => ':attribute harus diisi',
        ]);
 
        if ($validator->fails()) {

            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 422]);
        }

        $update =  Customer::findOrFail($customer);
        $update->name = $request->get('name');
        $update->address = $request->get('address');
        $update->save();

        if($request->file('photo')){
            $file= $request->file('photo');
            if( in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']) ) //validate image
            {
                $filename= date('YmdHis')."_". Str::slug($update->name) . "." . $file->getClientOriginalExtension();
                $file->move(public_path('avatars'), $filename);

                Customer::where('id', $customer)->update([ 'avatar' => "avatars/". $filename ]);
            }

        }

        return response()->json(['success' => true, 'message' => 'Data pelanggan berhasil diupdate']);
        
    }

    public function destroy($customer, Request $request)
    {
        Customer::where('id', $customer)->delete();

        return response()->json(['success' => true, 'message' => 'Data pelanggan berhasil dihapus']);
    }


}

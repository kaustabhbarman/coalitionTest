<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $inventory = Storage::disk('local')->exists('inventory.json') ? json_decode(Storage::disk('local')->get('inventory.json')) : [];
        return view('welcome', compact('inventory'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'product' => 'bail|required',
            'quantity' => 'bail|required|numeric',
            'price' => 'bail|required|numeric'
        ]);

        try{
            $inventory = Storage::disk('local')->exists('inventory.json') ? json_decode(Storage::disk('local')->get('inventory.json')) : [];

            $inputData = $request->only(['product', 'quantity', 'price']);
            $inputData['datetime'] = date('Y-m-d H:i:s');
            $inputData['totalvalue'] = request('quantity') * request('price');

            array_push($inventory, $inputData);
            Storage::disk('local')->put('inventory.json', json_encode($inventory));

            return response()->json($inputData);

        } catch (\Exception $e){
            return response()->json(['status'=>401, 'message'=>$e]);
        }
    }
}

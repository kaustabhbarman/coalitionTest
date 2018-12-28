<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $total = 0;
        $inventory = Storage::disk('local')->exists('inventory.json') ? json_decode(Storage::disk('local')->get('inventory.json')) : [];
        foreach ($inventory as $item) {
            $total += $item->totalvalue;
        }
        return view('welcome', compact(['inventory', 'total']));
    }

    public function create(Request $request)
    {
        $request->validate([
            'product' => 'bail|required',
            'quantity' => 'bail|required|numeric',
            'price' => 'bail|required|numeric'
        ]);

        try{
            $total = 0;
            $inventory = Storage::disk('local')->exists('inventory.json') ? json_decode(Storage::disk('local')->get('inventory.json')) : [];

            $inputData = $request->only(['product', 'quantity', 'price']);
            $inputData['datetime'] = date('Y-m-d H:i:s');
            $inputData['totalvalue'] = request('quantity') * request('price');

            array_push($inventory, $inputData);

            Storage::disk('local')->put('inventory.json', json_encode($inventory));

            $inventory = Storage::disk('local')->exists('inventory.json') ? json_decode(Storage::disk('local')->get('inventory.json')) : [];
            foreach ($inventory as $item) {
                $total += $item->totalvalue;
            }

            return response()->json(['row'=>$inputData, 'total'=>$total]);

        } catch (\Exception $e){
            return response()->json(['status'=>401, 'message'=>$e]);
        }
    }
}

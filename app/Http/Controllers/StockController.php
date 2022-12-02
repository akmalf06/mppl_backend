<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Exception;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query();
        $branch_id = 1;
        $query->where('branch_id', $branch_id);
        if($request->input('keyword'))
        {
            $query->where('name', $request->input('keyword'));
        }
        $data = $query->get()->toArray();
        return $this->sendData($data);
    }

    public function show(Request $request)
    {
        $data = Stock::find($request->route('id'));
        return $this->sendData($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'mitra_name' => 'required|string|max:128',
            'mitra_wa' => 'required|numeric|digits_between:11,15',
            'stock_number' => 'required|numeric',
            'branch_id' => 'nullable|numeric|exists:branches,id',
            'image' => 'required|image|max:4096'
        ]);

        $image_path = $request->file('image')->store('stocks');

        //TODO
        //get branch_id from user
        $branch_id = 1;
        if($request->has('branch_id'))
        {
            $branch_id = $request->input('branch_id');
        }

        Stock::create([
            'name' => $request->input('name'),
            'mitra_name' => $request->input('mitra_name'),
            'mitra_wa' => $request->input('mitra_wa'),
            'stock_number' => $request->input('stock_number'),
            'branch_id' => $branch_id,
            'image' => $image_path
        ]);

        return $this->sendOk();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'mitra_name' => 'required|string|max:128',
            'mitra_wa' => 'required|numeric|digits_between:11,15',
            'stock_number' => 'required|numeric',
            'branch_id' => 'nullable|numeric|exists:branches,id',
            'image' => 'nullable|image|max:4096'
        ]);

        $image_path = null;
        if($request->hasFile('image'))
        {
            $image_path = $request->file('image')->store('stocks');
        }

        $stock = Stock::find($request->route('id'));

        if(!$stock)
        {
            throw new Exception('Bahan baku tidak ditemukan');
        }

        //TODO
        //get branch_id from user
        $branch_id = 1;
        if($request->has('branch_id'))
        {
            $branch_id = $request->input('branch_id');
        }
        
        $stock->name = $request->input('name');
        $stock->mitra_name = $request->input('mitra_name');
        $stock->mitra_wa = $request->input('mitra_wa');
        $stock->stock_number = $request->input('stock_number');
        $stock->branch_id = $branch_id;
        if($image_path)
        {
            $stock->image = $image_path;
        }
        $stock->save();
    
        return $this->sendOk();   
    }
}

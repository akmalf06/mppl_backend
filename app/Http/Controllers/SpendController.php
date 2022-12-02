<?php

namespace App\Http\Controllers;

use App\Models\Spend;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpendController extends Controller
{
    public function index(Request $request)
    {
        $query = Spend::query();
        $branch_id = $request->route('branchId');
        $query->where('branch_id', $branch_id);
        if($request->input('type'))
        {
            $query->where('type', $request->input('type'));
        }
        $data = $query->get()->toArray();
        return $this->sendData($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(Spend::TYPES_SPEND)],
            'description' => 'required|string|max:2048',
            'amount' => 'required|numeric|digits_between:1,6',
            'branch_id' => 'nullable|numeric|exists:branches,id'
        ]);
        $branch_id = $request->route('branchId');
        if($request->has('branch_id'))
        {
            $branch_id = $request->input('branch_id');
        }

        Spend::create([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'branch_id' => $branch_id
        ]);
        return $this->sendOk();
    }
}

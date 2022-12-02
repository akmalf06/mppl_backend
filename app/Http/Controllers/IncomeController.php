<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::query();
        $branch_id = 1;
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
            'type' => ['required', Rule::in(Income::TYPES_INCOME)],
            'description' => 'required|string|max:2048',
            'amount' => 'required|numeric|digits_between:1,6',
            'branch_id' => 'nullable|numeric|exists:branches,id'
        ]);
        //TODO
        //get branch_id from user
        $branch_id = 1;
        if($request->has('branch_id'))
        {
            $branch_id = $request->input('branch_id');
        }

        Income::create([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'branch_id' => $branch_id
        ]);
        return $this->sendOk();
    }
}

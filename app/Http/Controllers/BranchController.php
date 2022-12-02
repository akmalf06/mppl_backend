<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $data = Branch::query()->with(
            [
                "incomes" => function ($query){
                    $query->selectRaw("SUM(CAST('amount' AS UNSIGNED))");
                },
                "spends" => function ($query){
                    $query->selectRaw("SUM(CAST('amount' AS UNSIGNED))");
                },
            ]
        )->get();
        $this->sendData($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'address' => 'required|string|max:512'
        ]);

        Branch::create([
            'name' => $request->input('name'),
            'address' => $request->input('address')
        ]);

        return $this->sendOk();
    }

    public function show(Request $request)
    {
        $branch = Branch::with('income', 'spend')->find($request->route('id'));
        if(!$branch){
            throw new Exception('Cabang tidak ditemukan');
        }
        $total_income = $branch->incomes->sum(function($income){
            return (int)$income['amount'];
        });
        $total_spend = $branch->spends->sum(function($spend){
            return (int)$spend['amount'];
        });
        $data = array(
            "name" => $branch->name,
            "address" => $branch->address,
            "summary" => array(
                "income" => $total_income,
                "spend" => $total_spend
            ),
            "spends" => $branch->spends,
            "incomes" => $branch->incomes
        );

        return $this->sendData($data);
    }
}

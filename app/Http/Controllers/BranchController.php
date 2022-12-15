<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $raw = Branch::query()->with(     
            [
                "incomes" => function($query){
                    $query->select('id', 'amount', 'branch_id');
                },
                "spends" => function($query){
                    $query->select('id', 'amount', 'branch_id');
                }
            ]
        )->get()->toArray();
        $data = array_map(
            function($item){
                $incomes = collect($item['incomes']);
                $spends = collect($item['spends']);
                $total_income = $incomes->sum(
                    fn($income) => $income['amount']
                );
                $total_spend = $spends->sum(
                    fn($spend) => $spend['amount']
                );
                $summary = [
                    "income" => $total_income,
                    "spend" => $total_spend
                ];
                $item['summary'] = $summary;
                return $item;
            },
            $raw
        );
        return $this->sendData($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'address' => 'required|string|max:512',
            "supervisor_id" => 'required|numeric|exists:users,id'
        ]);

        DB::beginTransaction();
        try{
            Branch::create([
                'name' => $request->input('name'),
                'address' => $request->input('address')
            ]);
    
            $user = User::find($request->input('supervisor_id'));
            $user->is_supervisor = true;
            $user->save();
            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

        return $this->sendOk();
    }

    public function show(Request $request)
    {
        $branch = Branch::with('incomes', 'spends')->find($request->route('branchId'));
        if(!$branch){
            throw new Exception('Cabang tidak ditemukan');
        }
        $total_income = $branch->incomes->sum(function($income){
            return $income['amount'];
        });
        $total_spend = $branch->spends->sum(function($spend){
            return $spend['amount'];
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

    public function employee(Request $request)
    {
        $employee = User::where('branch_id', $request->route('branchId'))->get(['id', 'name']);
        return $this->sendData($employee);
    }
}

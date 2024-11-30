<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DashboardControlller extends Controller
{
    private $status   = true;
    private $quantity = [];
    private $sum      = [];

    public function index()
    {
        
        try {
            $this->quantityByCategory();
            $this->sumByCategory();

        } catch (\Throwable $th) {
            $status = false;        
        }

        return response()->json([
            'status'    => $this->status,
            "quantity"  => $this->quantity,
            "sum"       => $this->sum
        ]);
    }

    public function quantityByCategory()
    {
        $total = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('movements', 'products.id', '=', 'movements.product_id')
            ->select('categories.name as category_name', DB::raw('SUM(movements.quantity) as total_quantity'))
            ->groupBy('categories.name')
            ->get();
          
        $labels = [];
        $amount = [];

        foreach ($total as $key => $value) {
        
            array_push($labels, $value->category_name);
            array_push($amount, intval($value->total_quantity));
        }
    
        $this->quantity = [ 
            'labels' => $labels,
            'amount' => $amount
        ];
    }

    public function sumByCategory()
    {
        $total = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('movements', 'products.id', '=', 'movements.product_id')
            ->select('categories.name as category_name', DB::raw('SUM(movements.price) as price'))
            ->groupBy('categories.name')
            ->get();
        
        
        $labels = [];
        $sum    = [];

        foreach ($total as $key => $value) {
        
            array_push($labels, $value->category_name);
            array_push($sum,    round($value->price, 2));
        }


        $this->sum = [ 
            'labels' => $labels,
            'sum'    => $sum  
        ];
    }
}

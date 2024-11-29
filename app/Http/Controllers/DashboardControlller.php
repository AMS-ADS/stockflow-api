<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DashboardControlller extends Controller
{
    public function bar()
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

        return response()->json([
            'status' => true,
            'body'   => [
                'labels' => $labels,
                'amount' => $amount
            ]
        ]);

    }
}

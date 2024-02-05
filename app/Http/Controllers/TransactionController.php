<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Dummy transaction data
        $transactions = [
            ['user' => 'John Doe', 'order' => 'Order #123', 'product' => 'Product A', 'amount' => 50.00],
            ['user' => 'Jane Smith', 'order' => 'Order #124', 'product' => 'Product B', 'amount' => 75.50],
            ['user' => 'Bob Johnson', 'order' => 'Order #125', 'product' => 'Product C', 'amount' => 30.25],
            ['user' => 'Alice Brown', 'order' => 'Order #126', 'product' => 'Product D', 'amount' => 90.00],
        ];

        return view('transactions.index', ['transactions' => $transactions]);
    }
}

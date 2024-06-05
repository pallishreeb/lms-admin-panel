<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\AnalogPayment;

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
        
        
        // Retrieve all payments
        $payments = AnalogPayment::all();

        // Return view with payments data
        return view('transactions.index', ['payments' => $payments,'transactions' => $transactions]);
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        // Find the payment by ID
        $payment = AnalogPayment::findOrFail($id);

        // Update payment status
        $payment->status = $request->input('status');
        $payment->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }
}

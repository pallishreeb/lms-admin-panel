<?php

namespace App\Http\Controllers;


use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentDetails = PaymentMethod::all();
        return view('payment_methods.index', compact('paymentDetails'));
    }

    public function create()
    {
        return view('payment_methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'payment_number' => 'required|string|max:255',
        ]);

        PaymentMethod::create($request->all());

        return redirect()->route('payment_details.index')->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $paymentDetail)
    {
        return view('payment_methods.edit', compact('paymentDetail'));
    }

    public function update(Request $request, PaymentMethod $paymentDetail)
    {
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'payment_number' => 'required|string|max:255',
        ]);

        $paymentDetail->update($request->all());

        return redirect()->route('payment_details.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentDetail)
    {
        $paymentDetail->delete();

        return redirect()->route('payment_details.index')->with('success', 'Payment method deleted successfully.');
    }
}

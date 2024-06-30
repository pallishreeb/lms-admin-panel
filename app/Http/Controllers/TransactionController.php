<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AnalogPayment;
use App\Models\PaymentMethod;
use App\Models\Category;
use App\Models\User;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Get all data initially
        $query = AnalogPayment::query();
    
        // Add filters here if needed
        if ($request->filled('student_id')) {
            $query->where('user_id', $request->student_id);
        }
    
        if ($request->filled('division')) {
            $query->where('division', 'like', '%' . $request->division . '%');
        }
    
        if ($request->filled('district')) {
            $query->where('district', 'like', '%' . $request->district . '%');
        }
    
        if ($request->filled('upazila')) {
            $query->where('upazilla', 'like', '%' . $request->upazila . '%');
        }
    
        if ($request->filled('school_name')) {
            $query->where('school_name', 'like', '%' . $request->school_name . '%');
        }
    
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
    
        if ($request->filled('payment_method')) {
            $query->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }
    
        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }
    
        if ($request->filled('payment_date')) {
            $query->whereDate('created_at', $request->payment_date);
        }
    
        if ($request->filled('payment_status')) {
            $query->where('status', $request->payment_status);
        }
        // Order by created_at in descending order to get the latest records first
        $query->orderBy('created_at', 'desc');
        // Fetch data
        $paymentDetails = $query->paginate(10); // 10 items per page
    
        // Retrieve students, classes, and payment methods for the drop-downs
        $students = User::all(); // Assuming User model represents students
        $classes = Category::all(); // Assuming Category model represents classes
        $paymentMethods = PaymentMethod::all();
    
        // Fetch divisions
        $divisions = Http::get('https://bdapis.com/api/v1.2/divisions')->json()['data'];
    
        return view('transactions.index', compact('paymentDetails', 'students', 'classes', 'paymentMethods', 'divisions'));
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
    public function destroy($id)
    {
        // Find the payment by ID
        $payment = AnalogPayment::findOrFail($id);

        // Delete the payment
        $payment->delete();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Analog payment deleted successfully.');
    }
}

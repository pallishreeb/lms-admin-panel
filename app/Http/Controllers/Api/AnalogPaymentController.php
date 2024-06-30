<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnalogPayment;
use App\Models\PaymentMethod;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class AnalogPaymentController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'payment_number' => 'required|string',
            'payment_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'division' => 'nullable|string',
            'district' => 'nullable|string',
            'upazilla' => 'nullable|string',
            'school_name' => 'nullable|string',
            'amount' => 'nullable|string',
            'student_name' => 'required|string',
            'mobile_number' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'payment_method' => 'nullable|string',
        ]);

           // Check if the current date is before or on 31st December
            $currentDate = Carbon::now();
            $expiryDate = Carbon::createFromDate($currentDate->year, 12, 31);
        if ($request->hasFile('payment_screenshot')) {
            $image = $request->file('payment_screenshot');
            $imageName = 'payment_images/' . time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('s3')->put($imageName, file_get_contents($image));
            $picUrl = Storage::disk('s3')->url($imageName);
            $validatedData['payment_screenshot'] = $picUrl;
            
        }
        $categoryId = $request->input('category_id');
        $category = Category::where('id', $categoryId)->first();
        $validatedData['class'] = $category->name; 
        $validatedData['status'] = 'pending'; // Set default status
        $validatedData['valid_until'] = $expiryDate->toDateString();
        AnalogPayment::create($validatedData);

        return response()->json(['message' => 'Payment details submitted successfully'], 201);
    }

        // Get all payment methods
    public function getAllPaymentMethods()
    {
        $paymentMethods = PaymentMethod::all();

        return response()->json($paymentMethods);
    }

    // Get all analog payments for a particular user by user id
    public function getAnalogPaymentsByUserId($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // $analogPayments = AnalogPayment::where('user_id', $userId)->get();
        $analogPayments = AnalogPayment::where('user_id', $userId)->with('category')->get();

        return response()->json($analogPayments);
    }
}

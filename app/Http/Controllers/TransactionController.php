<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AnalogPayment;
use App\Models\PaymentMethod;
use App\Models\Category;
use App\Models\User;
use App\Models\Notification;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

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
    

    // public function updateStatus(Request $request, $id)
    // {
    //     // Validate request data
    //     $request->validate([
    //         'status' => 'required|in:approved,rejected'
    //     ]);

    //     // Find the payment by ID
    //     $payment = AnalogPayment::findOrFail($id);

    //     // Update payment status
    //     $payment->status = $request->input('status');
    //     $payment->save();

    //     // Redirect back with success message
    //     return redirect()->back()->with('success', 'Payment status updated successfully.');
    // }

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

        // Get the user's FCM device token (assuming it's stored with the payment or user)
        $user = $payment->student; // Adjust if user retrieval differs
        $deviceToken = $user->device_token; // Make sure to replace this field with your actual field for storing FCM tokens

        // Prepare FCM notification data
        $title = "Payment Status Update";
        $message = "Your payment has been " . ucfirst($request->input('status')) . ".";
        $this->sendPushNotification($deviceToken, $title, $message);

        // Store the notification in the notifications table
        Notification::create([
            'user_id' => $user->id, // The ID of the user who receives the notification
            'title' => $title,
            'body' => $message,
            'book_id' => null, // If no book is associated, set to null
            'course_id' => null, // If no course is associated, set to null
        ]);
        // Redirect back with success message
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    private function sendPushNotification($deviceToken, $title, $message)
    {
        $sericeAccountPath = 'sohoj-pora-firebase-adminsdk-53ymn-2116c5dd5d.json';
        // Define the FCM server key
        //$access_token = env('FCM_SERVER_KEY'); // Store this securely, e.g., in your .env file
        $projectId = env('fcm_project_id'); # INSERT COPIED PROJECT ID
        // Define the FCM API endpoint
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/sohoj-pora/messages:send';
    
        // Prepare the notification payload
        $notificationData = [
            'body' => $message,
            'title' => $title,
        ];
    
        // Prepare the data payload
        $data = [
            "message" => [
                "token" => $deviceToken,
                "notification" =>  $notificationData,
            ]
        ];
    
        // Get access token from private key file
        $credentialsFilePath = Storage::path('json/sohoj-pora-firebase-adminsdk-53ymn-2116c5dd5d.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Execute the cURL request
        $result = curl_exec($ch);
    
        // Check for cURL errors
        if ($result === FALSE) {
            error_log('FCM Send Error: ' . curl_error($ch));
            // Optionally, log to file
            \Log::error('FCM Send Error', ['error' => curl_error($ch)]);
        }
    
        // Close the cURL session
        curl_close($ch);
    
        // Optional: Log the FCM response
        error_log('FCM Send Response: ' . $result);
       
        
        return $result;
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

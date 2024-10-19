<?php

namespace App\Http\Controllers;

use App\Models\AppConfigurations;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppConfigController extends Controller
{
    public function index(Request $request)
    {
      $configurations = AppConfigurations::all();
      $notificationPreference = AppConfigurations::where('config_key', 'notification_preference')->value('config_value');
      return view('admin.config', compact('notificationPreference', 'configurations'));
    }
    
public function updateNotificationPreference(Request $request)
{
    $request->validate([
        'notification_preference' => 'required|in:email,sms',
    ]);

    AppConfigurations::updateOrCreate(
        ['config_key' => 'notification_preference'],
        ['config_value' => $request->input('notification_preference')]
    );

    return redirect()->back()->with('success', 'Notification preference updated successfully.');
}

public function dashboard(Request $request){
  $booksCount = Book::count();
  $categoriesCount = Category::count();
  $coursesCount = Course::count();
  $usersCount = User::count();
 // Fetch currently logged-in users
//  $loggedInUsers = DB::table('sessions')->where('user_id', '!=', null)->get();
 // Fetch currently logged-in devices users with names
 $loggedInUsers = DB::table('sessions')
 ->join('users', 'sessions.user_id', '=', 'users.id')
 ->select('sessions.id as session_id', 'users.name', 'sessions.user_id', 'sessions.last_activity', 'sessions.device_info','sessions.user_agent')
 ->whereNotNull('sessions.user_id')
 ->get();

  return view('admin.dashboard', [
      'booksCount' => $booksCount,
      'categoriesCount' => $categoriesCount,
      'coursesCount' => $coursesCount,
      'usersCount' => $usersCount,
      'loggedInUsers' => $loggedInUsers
  ]);
}

public function logout($id)
{
    DB::table('sessions')->where('id', $id)->delete();
    return redirect()->route('admin.dashboard')->with('success', 'User logged out successfully.');
}
}

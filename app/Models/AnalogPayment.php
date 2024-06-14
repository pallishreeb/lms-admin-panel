<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalogPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number', 
        'payment_screenshot', 
        'division', 
        'district', 
        'upazilla', 
        'school_name', 
        'class', 
        'student_name', 
        'mobile_number',
        'user_id',
        'category_id',
        'status',
        'amount',
        'payment_method',

    ];
        // Define the relationship with the User model (for students)
        public function student()
        {
            return $this->belongsTo(User::class, 'user_id');
        }
    
        // Define the relationship with the Category model (for classes)
        public function category()
        {
            return $this->belongsTo(Category::class, 'category_id');
        }
}

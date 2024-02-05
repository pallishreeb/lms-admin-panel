<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\AwsSns\SnsMessage;

class OtpNotification extends Notification
{
    protected $mobileNumber;
    protected $otp;

    public function __construct($mobileNumber, $otp)
    {
        $this->mobileNumber = $mobileNumber;
        $this->otp = $otp;
    }

    public function toSns($notifiable)
    {
        return SnsMessage::create()
            ->body("Your OTP is: {$this->otp}")
            ->transactional()
            ->promotion()
            ->type('sms')
            ->phoneNumber($this->mobileNumber); // Add this line to specify the phone number
    }
}

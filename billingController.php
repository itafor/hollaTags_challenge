<?php
  
use Illuminate\Http\Request;

use App\Http\Requests;
use Twilio\Rest\Client;
use App\Models\User;
use App\Models\UserBill;

class billingController
{
	//This method handles the billing
    public function billUsers($amount_to_bill,$bill_month,$bill_item)
    {
    	$users =User::all();
    	if (count($users) >=1) {
    		foreach ($users as $key => $user) {
    			self::storeUserBillAndSendSMS($user->id,$amount_to_bill,$bill_month,$bill_item);
    		}
    	}

    	return 'Done';
    }

//This method stores the bill details to a table called user_bills in the Database and thereafter send SMS to the users
    public static function storeUserBillAndSendSMS($user_id,$amount_to_bill,$bill_month,$bill_item)
    {
    	$bill = UserBill::create([
            'user_id' => $user_id,
            'amount_to_bill' => $amount_to_bill
            'bill_month' => $bill_month,
            'bill_item' => $bill_item,
        ]); 

    	$user= DB::table('users')->where('user_id',$user_id)
    	->select('users.*')->first();

    	if($bill)
    	{
    		self::sendSMS($user->mobile_number,$amount_to_bill,$bill_month,$bill_item);
    	}
  return $bill;
    }

//This method handles SMS sending functionality
   public static function sendSMS($mobile_number,$amount_to_bill,$bill_month,$bill_item)
   {
 $sid    = "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$token  = "your_auth_token";
$twilio = new Client($sid, $token);

$message = $twilio->messages
          ->create($mobile_number, // to
           [
               "body" => "This is to notify you that, your ".$bill_month." ".$bill_item."bill is".$amount_to_bill."Kindly pay as soon as possible. Thanks",
               "from" => "+2347065907948"
           ]
         );

   }
}

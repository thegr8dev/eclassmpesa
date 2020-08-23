<?php

namespace Thegr8dev\Eclassmpesa\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use SmoDav\Mpesa\Engine\Core;
use SmoDav\Mpesa\Native\NativeCache;
use SmoDav\Mpesa\Native\NativeConfig;
use SmoDav\Mpesa\Laravel\Facades\Registrar;
use SmoDav\Mpesa\Laravel\Facades\Simulate;
use SmoDav\Mpesa\Laravel\Facades\STK;
use Illuminate\Http\Response;
use Log;
use Session;
use DB;
use Auth;
use App\Cart;
use App\Wishlist;
use App\Order;
use App\Currency;
use App\Mail\SendOrderMail;
use App\Notifications\UserEnroll;
use App\Course;
use App\Http\Controllers\Controller;
use App\User;
use Notification;
use Carbon\Carbon;
use App\InstructorSetting;
use App\PendingPayout;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Mail as FacadesMail;

class PayPesaController extends Controller
{
    public function stkpush(Request $request){
     
        $config = new NativeConfig();
        $cache = new NativeCache($config->get('cache_location'));

        $core = new Core(new Client, $config, $cache);

        $stk = new STK($core);
        
        $amount = round($request->amount);
        
        try{
            $response = STK::push(1, $request->phone, 'Payment For Eclass Order ID', 'Payment by '.Auth::user()->name);
            $y = json_encode($response);
            $res = json_decode($y,true);
            $checkoutid = $res['CheckoutRequestID'];
            return view('eclassmpesa::mpesawait',compact('checkoutid'));
        }catch(\Exception $e){
              return redirect('/all/cart')->with('delete',$e->getMessage());
        }

    }
    
    public function verifypay($paymentid){
        
       sleep(1);
        
       $result =  \DB::table('mpesatxn')->where('checkoutid',$paymentid)->first();
    
       if($result){
           
           if($result->rcode != 0){
                
                \DB::table('mpesatxn')->where('checkoutid',$paymentid)->delete();
                return response()->json([ 'resultCode' => $result->rcode, 'msg' => $result->rdesc ]);
            
           }else{
               
                    $txnid = $result->txnid;
                    
                    $currency = Currency::first();

            $carts = Cart::where('user_id', Auth::User()->id)->get();

            foreach ($carts as $cart) {
                if ($cart->offer_price != 0) {
                    $pay_amount = $cart->offer_price;
                } else {
                    $pay_amount = $cart->price;
                }

                if ($cart->disamount != 0 || $cart->disamount != null) {
                    $cpn_discount = $cart->disamount;
                } else {
                    $cpn_discount = '';
                }

                $lastOrder = Order::orderBy('created_at', 'desc')->first();

                if (!$lastOrder) {
                    // We get here if there is no order at all
                    // If there is no number set it to 0, which will be 1 at the end.
                    $number = 0;
                } else {
                    $number = substr($lastOrder->order_id, 3);
                }

                if ($cart->type == 1) {
                    $bundle_id = $cart->bundle_id;
                    $bundle_course_id = $cart->bundle->course_id;
                    $course_id = null;
                    $duration = null;
                    $instructor_payout = null;
                    $todayDate = null;
                    $expireDate = null;
                    $instructor_id = $cart->bundle->user_id;
                } else {

                    if ($cart->courses->duration_type == "m") {

                        if ($cart->courses->duration != null && $cart->courses->duration != '') {
                            $days = $cart->courses->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    } else {

                        if ($cart->courses->duration != null && $cart->courses->duration != '') {
                            $days = $cart->courses->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }

                    }

                    $setting = InstructorSetting::first();

                    if ($cart->courses->instructor_revenue != null) {
                        $x_amount = $pay_amount * $cart->courses->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {

                        if (isset($setting)) {
                            if ($cart->courses->user->role == "instructor") {
                                $x_amount = $pay_amount * $setting->instructor_revenue;
                                $instructor_payout = $x_amount / 100;
                            } else {
                                $instructor_payout = 0;
                            }

                        } else {
                            $instructor_payout = 0;
                        }
                    }

                    $bundle_id = null;
                    $course_id = $cart->course_id;
                    $bundle_course_id = null;
                    $duration = $cart->courses->duration;
                    $instructor_id = $cart->courses->user_id;
                }

                $created_order = Order::create([
                    'course_id' => $course_id,
                    'user_id' => Auth::User()->id,
                    'instructor_id' => $instructor_id,
                    'order_id' => '#' . sprintf("%08d", intval($number) + 1),
                    'transaction_id' => $txnid,
                    'payment_method' => strtoupper(env('PAYU_DEFAULT')),
                    'total_amount' => $pay_amount,
                    'coupon_discount' => $cpn_discount,
                    'currency' => $currency->currency,
                    'currency_icon' => $currency->icon,
                    'duration' => $duration,
                    'enroll_start' => $todayDate,
                    'enroll_expire' => $expireDate,
                    'bundle_id' => $bundle_id,
                    'bundle_course_id' => $bundle_course_id,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]
                );

                Wishlist::where('user_id', Auth::User()->id)->where('course_id', $cart->course_id)->delete();

                Cart::where('user_id', Auth::User()->id)->where('course_id', $cart->course_id)->delete();

                if ($instructor_payout != 0) {
                    if ($created_order) {

                        if ($cart->type == 0) {

                            if ($cart->courses->user->role == "instructor") {

                                $created_payout = PendingPayout::create([
                                    'user_id' => $cart->courses->user_id,
                                    'course_id' => $cart->course_id,
                                    'order_id' => $created_order->id,
                                    'transaction_id' => $txnid,
                                    'total_amount' => $pay_amount,
                                    'currency' => $currency->currency,
                                    'currency_icon' => $currency->icon,
                                    'instructor_revenue' => $instructor_payout,
                                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                ]
                                );
                            }
                        }

                    }
                }

                if ($created_order) {
                    try {

                        /*sending email*/
                        $x = 'You are successfully enrolled in a course';
                        $order = $created_order;
                        FacadesMail::to(FacadesAuth::User()->email)->send(new SendOrderMail($x, $order));

                    } catch (\Swift_TransportException $e) {
                        Session::flash('deleted', 'Payment Successfully ! but Invoice will not sent because of error in mail configuration !');
                        return redirect('/');
                    }
                }

                if ($cart->type == 0) {

                    if ($created_order) {
                        // Notification when user enroll
                        $cor = Course::where('id', $cart->course_id)->first();

                        $course = [
                            'title' => $cor->title,
                            'image' => $cor->preview_image,
                        ];

                        $enroll = Order::where('course_id', $cart->course_id)->get();

                        if (!$enroll->isEmpty()) {
                            foreach ($enroll as $enrol) {
                                $user = User::where('id', $enrol->user_id)->get();
                                Notification::send($user, new UserEnroll($course));
                            }
                        }
                    }
                }
            }
                    
                
                \DB::table('mpesatxn')->where('checkoutid',$paymentid)->delete();
                    
                return response()->json(['resultCode' => $result->rcode, 'msg' => $result->rdesc, 'txnid' => $result->txnid]);
           }
           
           
       }else{
           return response()->json(['resultCode' => 404, 'msg' => 'Timeout']);
       }
       
       
    }
    
    public function registerURL($request){
        
        $conf = url('api/payment/confirm/callback');
        $val  = url('api/payment/validation');
        
        $response = Registrar::submit($request->MPESA_SHORTCODE, $conf, $val);
        return $response;
    }

    public function adminsettings(){
        return view('eclassmpesa::mpesasetting');
    }

    public function updatesetting(Request $request){

        if(!isset($request->MPESA_ENABLE)){

             $env = $this->changeEnv([
                'MPESA_ENABLE' => 0
             ]);

             return back()->with('success','MPesa is disabled successfully !');

        }else{

            $response = $this->registerURL($request);
            
            $y = json_encode($response);
            $res = json_decode($y,true);

            if($res['ResponseDescription'] == 'success'){

                $env = $this->changeEnv([
                    'MPESA_KEY'       => $request->MPESA_KEY,
                    'MPESA_SECRET'    => $request->MPESA_SECRET,
                    'MPESA_INITIATOR' => $request->MPESA_INITIATOR,
                    'MPESA_PAYBILL'   => $request->MPESA_PAYBILL,
                    'MPESA_SHORTCODE' => $request->MPESA_SHORTCODE,
                    'MPESA_PASSKEY'   => $request->MPESA_PASSKEY,
                    'MPESA_ENABLE'    => isset($request->MPESA_ENABLE) ? 1 : 0
                ]);

                return back()->with('success','MPesa settings updated successfully !');
            }else{
                return back()->with('delete',$res['ResponseDescription']);
            }
        
        
        }
    }
    
    public function createValidationResponse($result_code, $result_description){
        $result=json_encode(["ResultCode"=>$result_code, "ResultDesc"=>$result_description]);
        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }

    public function mpesaValidation(Request $request)
    {
        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->createValidationResponse($result_code, $result_description);
    }
    
    public function callback(Request $request)
    {
        $content = json_decode($request->getContent(),true);
        
        if($content != NULL){
            if($content['Body']['stkCallback']['ResultCode'] == 0){
                
                $posts = json_encode($content['Body']['stkCallback']);
                
                \DB::table('mpesatxn')->insert([
                    'checkoutid' =>  $content['Body']['stkCallback']['CheckoutRequestID'],
                    'rcode' => $content['Body']['stkCallback']['ResultCode'],
                    'rdesc' => $content['Body']['stkCallback']['ResultDesc'],
                    'txnid' =>  $content['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value']   
                ]);
                
                @file_put_contents(public_path().'/'.$content['Body']['stkCallback']['CheckoutRequestID'].'.json', $posts . PHP_EOL);
                  
            }else{
                
                $posts = json_encode($content['Body']['stkCallback']);
                
                \DB::table('mpesatxn')->insert([
                    'checkoutid' =>  $content['Body']['stkCallback']['CheckoutRequestID'],
                    'rcode' => $content['Body']['stkCallback']['ResultCode'],
                    'rdesc' => $content['Body']['stkCallback']['ResultDesc'],
                    'txnid' =>  NULL   
                ]);
                
                @file_put_contents(public_path().'/'.$content['Body']['stkCallback']['CheckoutRequestID'].'.json', $posts . PHP_EOL);
                
            }
        }else{
            Log::error('Transcation is corrupted.');
        }
       
    }

    protected function changeEnv($data = array())
    {
        if ( count($data) > 0 ) {

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){
              // Loop through .env-data
              foreach($env as $env_key => $env_value){
                // Turn the value into an array and stop after the first split
                // So it's not possible to split e.g. the App-Key by accident
                $entry = explode("=", $env_value, 2);

                // Check, if new key fits the actual .env-key
                if($entry[0] == $key){
                    // If yes, overwrite it with the new one
                    $env[$env_key] = $key . "=" . $value;
                } else {
                    // If not, keep the old one
                    $env[$env_key] = $env_value;
                }
              }
            }

            // Turn the array back to an String
            $env = implode("\n\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;

        } else {

          return false;
        }
    }   
}

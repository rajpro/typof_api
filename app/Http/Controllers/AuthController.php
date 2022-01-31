<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Store;
use App\Models\DomainSetup;

use App\Mail\EmailVerify;
use App\Mail\SendMailOtp;
use App\Mail\WelcomeMail;

use App\Jobs\SendMobileOtp;


class AuthController extends Controller
{
	/**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $data = ['status' => true];

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['error'] = $validator->errors()->first();
        }else{
            $user = [
                "name" => $request['name'],
                "email" => $request['email'],
                "password" => Hash::make($request['password']),
            ];

            $user = User::create($user);
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $ref_code = '';
            for ($i = 0; $i < 6; $i++) {
                $ref_code .= $characters[rand(0, 51)];
            }
            $user->setting()->create(['type'=>'custom_fields','data'=>['wallet'=>0, 'referral_code'=>$ref_code]]);
            $data['message'] = "User Created Successfully";
            Mail::to($request['email'])->send(new EmailVerify());
        }

        return response()->json($data);
    }

    public function email_verify(Request $request)
    {
        $data = ['status' => true];
        $user = User::find(Auth::id());
        $user->email_verified_at = date("Y-m-d H:i:s");
        $user->update();
        return response()->json($data);
    }

    public function send_otp(Request $request)
    {
        $data = ['status' => true];
        $user = User::find(Auth::id());
        Mail::to($user->email)->send(new SendMailOtp($request['otp']));
        return response()->json($data);
    }

    public function send_mobile_otp(Request $request)
    {
        $data = ['status' => true, 'message'=>"OTP Sent"];
        $message = "Your OTP from Typof is ". $request['otp'];
        dispatch(new SendMobileOtp($request['phone'], $message));
        return response()->json($data);
    }

    public function check_email(Request $request)
    {
        $data = ['status' => true];

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|email',
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['error'] = $validator->errors()->first();
        }else{
            $data['message'] = "Email Approved.";
        }

        return response()->json($data);
    }

    public function check_mobile(Request $request)
    {
        $data = ['status' => true];

        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users',
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['error'] = $validator->errors()->first();
        }else{
            $data['message'] = "Mobile Approved";
        }

        return response()->json($data);
    }

    public function check_store(Request $request)
    {
        $data = ['status' => true];

        $user = Auth::user();
        if(!empty($user->store_id)){
            $data['message'] = "Store Already Created";
        }else{
            $data['status'] = false;
            $data['error'] = "Store Not Created";
        }
        return response()->json($data);
    }

    public function create_store(Request $request)
    {
        $data = ['status' => true];

        $validator = Validator::make($request->all(), [
            'store_name' => 'required|max:255',
            'mobile' => 'required|unique:store_table',
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['message'] = $validator->errors()->first();
            return response()->json($data);
        }else{
            $url = $this->_storename($request['store_name']);
            if(Store::where('website', $url)->count() > 0){
                $data['status'] = false;
                $data['message'] = 'Store Name is Already Exist';
                return response()->json($data);
            }
            $store = [
                'store_name' => $request['store_name'],
                'country' => 'IND',
                'website' => $url,
                'folder_name' => 'default_template',
            ];
            $store = Store::create($store);
            $user = User::where('id', Auth::user()->id)->first();
            $store->email_id = Auth::user()->email;
            $store->mobile = $request['mobile'];
            $store->update();
            
            $user->store_id = $store->store_id;
            $user->phone = $request['mobile'];
            $user->update();
            $user->assignRole(Role::where('name', 'admin')->first());
            $s = $store->setting()->where('type', 'commission')->first();
            if(!empty($s)){
                $s->update(['data'=>['percent'=>10]]);
            }else{
                $store->setting()->create(['type'=>'commission', 'data'=>['percent'=>10]]);
            }
            DomainSetup::create(['store_id'=>$store->store_id, 'primary'=>$url]);

            $this->__create_modules($store);
            Mail::to($user->email)->send(new WelcomeMail($user->name, $url));
            
        }

        return response()->json($data);
    }

    private function _storename($store_name) {
        $string = preg_replace("/(\s)/", '-', $store_name);
        $string = preg_replace("/([\W])/", '-', $string);
        $string = preg_replace('/(-)\1+/', '-', $string);
        $string = preg_replace('/^(-+)/', '', $string);
        $string = preg_replace('/(-)$/', '', $string);
        return strtolower($string).".typof.in";
    }

    private function __create_modules($store)
    {
        $store->setting()->create(['type'=>'category', 'data'=>['category_id'=>1]]);
        // $theme = Themes::where([
        //     ['category', 'like', "%1%"],
        //     ['default_theme', 1]
        //   ])->first();
        // if(!empty($theme)){
          
        // }
        $store->setting()->create(['type'=>'default_theme', 'data'=>['default_theme'=>1]]);
        $data = [
          "type" => "modules",
          "data" => [
            "banner" => true,
            "short_intro" => [
              "title" => "What we do?",
              "intro" => ""
            ],
            "blog" => 3
          ]
        ];
        $store->extra()->create($data);

        $setting = [
          "type" => 'modules',
          'data' => [
            'order' => [
              "banner",
              'short_intro',
              'shop_by_category'
            ]
          ]
        ];

        $store->setting()->create($setting);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
        ]);
    }
}
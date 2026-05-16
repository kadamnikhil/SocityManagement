<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Package;
use App\Models\Useritem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    public function getAppVersion(){
        $data = [
            'app_version' => '1.0.0+0',
            'url' => 'https://play.google.com/store/apps/details?id=com.example.app',
        ];
        return response()->json($data,200);
    }

    public function register(Request $request){
        $rules = [
            'first_name' =>  'required|max:191',
            'email' => 'required|email',
            'mobile' => 'required|numeric|unique:users,mobile|digits:10',
            'device_id' =>  'required|max:191',
        ];
        $customMessages = [
            'first_name.required' => 'Please enter name',
            'first_name.max' => 'Name should not be more than 191 characters',
            'email.required' => 'Please enter email',
            'email.email' => 'Please enter valid email',
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.unique' => 'Mobile number already exists',
            'mobile.digits' => 'Please enter 10 digits mobile',
            'device_id.required' => 'device_id is required',
        ];
        $validator = \Validator::make($request->all(),$rules,$customMessages);
        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()],400);
        }else{
            $random_number = 1234;
            $user = new User;
            $user->fill($request->all());
            $user->password = Hash::make($random_number);
            $user->save();

            //Send OTP
            // sendOTP($user->mobile, $random_number);

            return response()->json(['message' => 'OTP sent succesfully'],201);
        }
    }

    public function login(Request $request){
        $rules = [
            'mobile' => 'required|numeric|digits:10',
            'device_id' =>  'required|max:191',
        ];
        $customMessages = [
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.digits' => 'Please enter 10 digits mobile',
            'device_id.required' => 'device_id is required',
        ];

        $validator = \Validator::make($request->all(),$rules,$customMessages);
        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()],400);
        }else{
            $user = User::where('mobile',$request->mobile)->first();
            if($user){
                $random_number = 1234;
                $user->password = Hash::make($random_number);
                $user->device_id = $request->device_id;
                $user->save();

                //Send OTP
                // sendOTP($user->mobile, $random_number);

                return response()->json(['message' => 'OTP sent succesfully'],200);
            }else{
                return response()->json([
                    'errors' => ["mobile" => ["Mobile number does not exists"]]
                ], 401);
            }
        }
    }

    public function verifyOTP(Request $request){
        $rules = [
            'mobile' => 'required|numeric|digits:10',
            'otp' => 'required|digits:4',
        ];

        $customMessages = [
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.digits' => 'Please enter 10 digits mobile',
            'otp.required' => 'Please enter OTP',
            'otp.digits' => 'Please enter 4 digits for OTP',
        ];

        $validator = \Validator::make($request->all(),$rules,$customMessages);
        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()],400);
        }else{
            $credentials = ['mobile' => $request->mobile,'password' => $request->otp];
            if(\Auth::attempt($credentials)){
                $user = $request->user();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                if($request->remember_me){
                    $token->expires_at = Carbon::now()->addWeeks(1);
                }
                $token->save();
                return response()->json([
                        'message' => 'User logged in successfully',
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                    ],200);
            }else{
                return response()->json(['message' => 'OTP dose not match,Please enter correct OTP'],401);
            }
        }
    }

    public function resendOTP(Request $request){
        $rules = [
            'mobile' => 'required|numeric|digits:10',
        ];
        $customMessages = [
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.digits' => 'Please enter 10 digits mobile',
        ];
        $validator = \Validator::make($request->all(),$rules,$customMessages);
        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()],400);
        }else{
            $user = User::where('mobile',$request->mobile)->first();
            if($user){
                $random_number = rand(1111,9999);
                $user->password = Hash::make($random_number);
                $user->save();

                //Send OTP
                // sendOTP($user->mobile, $random_number);

                return response()->json(['message' => 'OTP sent succesfully'],200);
            }else{
                return response()->json(['message' => 'Mobile number does not exists'],401);
            }
        }
    }

    public function logout(Request $request){
        $user = auth('api')->user()->token();
        $user->revoke();
        return response()->json(['message' => 'User logged out succesfully'],200);
    }

    public function getUser(Request $request){
        $user = User::find(auth('api')->user()->id);
        return response()->json(new UserResource($user))->setStatusCode(200);
    }

    public function updateUser(Request $request){
        $user = User::find(auth('api')->user()->id);
        $rules = [
            'business_name' => 'required|max:50',
            'first_name' =>  'required|max:50',
            'last_name' =>  'required|max:50',
            'state' => 'required',
            'city' => 'required',
        ];
        $customMessages = [
            'business_name.required' => 'Please enter business name',
            'first_name.required' => 'Please enter first name',
            'last_name.required' => 'Please enter last name',
            'state.required' => 'Please select state',
            'city.required' => 'Please select city',
        ];
        $validator = \Validator::make($request->all(),$rules,$customMessages);
        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()],400);
        }else{
            $user->fill($request->all());
            $user->save();
            return response()->json(['message' => 'user updated successfully'],200);
        }
    }

    public function updateUserPhoto(Request $request){
        $rules = [
            'photo' => 'required|mimes:jpeg,jpg,png',
        ];
        $customMessages = [
            'photo.required' => 'Please select photo',
        ];
        $user = User::find(auth('api')->user()->id);
        if($request->hasFile('photo')){
            \Storage::disk('public')->delete($user->photo);
            $user->photo = \Storage::disk('public')->put('user-photos',$request->photo);
            $user->save();
            return response()->json(['message' => 'photo updated succesfully'],200);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Storage;
use App\Models\User;
use App\Models\Order;
use App\Models\Quote;
use App\Models\Slider;
use App\Models\Package;
use App\Models\Language;
use App\Models\Userposter;
use App\Models\Frameposter;
use App\Models\Socialframe;
use App\Models\Stickerimage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\Slider as SliderResource;

class HomeController extends Controller
{
    public function getSliders(Request $request){

        $sliders = Slider::get();
        $sliders = new SliderResource($sliders);
        return response()->json($sliders,200);
    }

    public function getStickercategories()
    {
        $stickercategory = \DB::table('stickercategories')->where('status', 'ACTIVE')->orderby('index', 'ASC')->get();
        return response()->json($stickercategory, 200);
    }
    public function getStickerimages( Request $request){
        if (isset($request->stickercategory_id)) {
            $stickerimages = Stickerimage::where('status', 'ACTIVE')->where('stickercategory_id', $request->stickercategory_id)->get();
        } else {
            $stickercategory = \DB::table('stickercategories')->where('status', 'ACTIVE')->orderby('index', 'ASC')->first();
            $stickerimages = Stickerimage::where('status', 'ACTIVE')->where('stickercategory_id', $stickercategory->id)->get();
        }
        return response()->json($stickerimages, 200);
    }


    public function getLanguages()
    {
        $languages = Language::where('status', 'ACTIVE')->orderby('index', 'ASC')->get();

        $empty_language = [
            'id' => 0,
            'status' => 'ACTIVE',
            'name' => 'All',
            'index' => 0,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => '2024-01-06T18:25:30.000000Z',
            'updated_at' => '2024-01-06T18:28:38.000000Z',
            'route_key' => '',
        ];

        $languages->prepend($empty_language);

        $languages = new LanguageResource($languages);
        return response()->json($languages, 200);
    }

    public function getQuotes()
    {
        $quotes = Quote::where('status', 'ACTIVE')->inRandomOrder()->limit(5)->get();
        $quotes = new QuoteResource($quotes);
        return response()->json($quotes, 200);
    }

    public function getPackages()
    {
        $packages = Package::where('status', 'ACTIVE')->orderby('index', 'ASC')->get();
        $packages = new PackageResource($packages);
        return response()->json($packages, 200);
    }

    public function getFrameposters(Request $request)
    {
        $frames = Frameposter::where('status', 'ACTIVE')
        ->when($request->frameposter_id, function ($query) use ($request) {
                return $query->where('id', $request->frameposter_id);
        })->get();

        return response()->json(['data' => FrameposterResource::collection($frames)], 200);
    }
    public function getSocialFrameposters(Request $request)
    {
        $social_frameposters =  Socialframe::where('status', 'ACTIVE')
        ->when($request->social_frameposter_id, function ($query) use ($request) {
                return $query->where('id', $request->social_frameposter_id);
        })->get();

        return response()->json(['data' => FrameposterResource::collection($social_frameposters)], 200);
    }


    public function getUser(Request $request)
    {
        $user = User::find(auth('api')->user()->id);


            if($user->business_logo_from_top_measurement == null){
                $user->business_logo_from_top_measurement = '210';
            }
            if($user->business_logo_from_left_measurement == null){
                $user->business_logo_from_left_measurement = '20';
            }
            if($user->business_personal_photo_from_top_measurement == null){
                $user->business_personal_photo_from_top_measurement = '220';
            }
            if($user->business_personal_photo_from_left_measurement == null){
                $user->business_personal_photo_from_left_measurement = '220';
            }
            if($user->business_category_name_from_top_measurement == null){
                $user->business_category_name_from_top_measurement = '230';
            }
            if($user->business_category_name_from_left_measurement == null){
                $user->business_category_name_from_left_measurement = '130';
            }
            if($user->business_company_name_from_top_measurement == null){
                $user->business_company_name_from_top_measurement = '240';
            }
            if($user->business_company_name_from_left_measurement == null){
                $user->business_company_name_from_left_measurement = '140';
            }
            if($user->business_personal_name_from_top_measurement == null){
                $user->business_personal_name_from_top_measurement = '250';
            }
            if($user->business_personal_name_from_left_measurement == null){
                $user->business_personal_name_from_left_measurement = '50';
            }
            if($user->business_designation_from_top_measurement == null){
                $user->business_designation_from_top_measurement = '260';
            }
            if($user->business_designation_from_left_measurement == null){
                $user->business_designation_from_left_measurement = '90';
            }
            if($user->business_mobile_from_top_measurement == null){
                $user->business_mobile_from_top_measurement = '270';
            }
            if($user->business_mobile_from_left_measurement == null){
                $user->business_mobile_from_left_measurement = '70';
            }
            if($user->business_email_from_top_measurement == null){
                $user->business_email_from_top_measurement = '280';
            }
            if($user->business_email_from_left_measurement == null){
                $user->business_email_from_left_measurement = '80';
            }
            if($user->business_website_from_top_measurement == null){
                $user->business_website_from_top_measurement = '190';
            }
            if($user->business_website_from_left_measurement == null){
                $user->business_website_from_left_measurement = '90';
            }
            if($user->business_facebook_account_from_top_measurement == null){
                $user->business_facebook_account_from_top_measurement = '200';
            }
            if($user->business_facebook_account_from_left_measurement == null){
                $user->business_facebook_account_from_left_measurement = '100';
            }
            if($user->business_twitter_account_from_top_measurement == null){
                $user->business_twitter_account_from_top_measurement = '210';
            }
            if($user->business_twitter_account_from_left_measurement == null){
                $user->business_twitter_account_from_left_measurement = '10';
            }
            if($user->business_instagram_account_from_top_measurement == null){
                $user->business_instagram_account_from_top_measurement = '260';
            }
            if($user->business_instagram_account_from_left_measurement == null){
                $user->business_instagram_account_from_left_measurement = '150';
            }
            if($user->political_logo_from_top_measurement == null){
                $user->political_logo_from_top_measurement = '240';
            }
            if($user->political_logo_from_left_measurement == null){
                $user->political_logo_from_left_measurement = '80';
            }
            if($user->political_personal_photo_from_top_measurement == null){
                $user->political_personal_photo_from_top_measurement = '220';
            }
            if($user->political_personal_photo_from_left_measurement == null){
                $user->political_personal_photo_from_left_measurement = '250';
            }
            if($user->political_party_name_from_top_measurement == null){
                $user->political_party_name_from_top_measurement = '280';
            }
            if($user->political_party_name_from_left_measurement == null){
                $user->political_party_name_from_left_measurement = '90';
            }
            if($user->political_personal_name_from_top_measurement == null){
                $user->political_personal_name_from_top_measurement = '210';
            }
            if($user->political_personal_name_from_left_measurement == null){
                $user->political_personal_name_from_left_measurement = '186';
            }
            if($user->political_designation_from_top_measurement == null){
                $user->political_designation_from_top_measurement = '250';
            }
            if($user->political_designation_from_left_measurement == null){
                $user->political_designation_from_left_measurement = '239';
            }
            if($user->political_mobile_from_top_measurement == null){
                $user->political_mobile_from_top_measurement = '210';
            }
            if($user->political_mobile_from_left_measurement == null){
                $user->political_mobile_from_left_measurement = '99';
            }
            if($user->political_email_from_top_measurement == null){
                $user->political_email_from_top_measurement = '260';
            }
            if($user->political_email_from_left_measurement == null){
                $user->political_email_from_left_measurement = '196';
            }
            if($user->political_website_from_top_measurement == null){
                $user->political_website_from_top_measurement = '260';
            }
            if($user->political_website_from_left_measurement == null){
                $user->political_website_from_left_measurement = '145';
            }
            if($user->political_facebook_account_from_top_measurement == null){
                $user->political_facebook_account_from_top_measurement = '240';
            }
            if($user->political_facebook_account_from_left_measurement == null){
                $user->political_facebook_account_from_left_measurement = '211';
            }
            if($user->political_twitter_account_from_top_measurement == null){
                $user->political_twitter_account_from_top_measurement = '200';
            }
            if($user->political_twitter_account_from_left_measurement == null){
                $user->political_twitter_account_from_left_measurement = '200';
            }
            if($user->political_instagram_account_from_top_measurement == null){
                $user->political_instagram_account_from_top_measurement = '230';
            }
            if($user->political_instagram_account_from_left_measurement == null){
                $user->political_instagram_account_from_left_measurement = '140';
            }

            if($user->personal_photo_from_top_measurement == null){
                $user->personal_photo_from_top_measurement = '280';
            }
            if($user->personal_photo_from_left_measurement == null){
                $user->personal_photo_from_left_measurement = '280';
            }
            if($user->personal_name_from_top_measurement == null){
                $user->personal_name_from_top_measurement = '345';
            }
            if($user->personal_name_from_left_measurement == null){
                $user->personal_name_from_left_measurement = '50';
            }
            if($user->personal_designation_from_top_measurement == null){
                $user->personal_designation_from_top_measurement = '340';
            }
            if($user->personal_designation_from_left_measurement == null){
                $user->personal_designation_from_left_measurement = '50';
            }
            if($user->personal_mobile_from_top_measurement == null){
                $user->personal_mobile_from_top_measurement = '320';
            }
            if($user->personal_mobile_from_left_measurement == null){
                $user->personal_mobile_from_left_measurement = '110';
            }
            if($user->personal_facebook_account_from_top_measurement == null){
                $user->personal_facebook_account_from_top_measurement = '280';
            }
            if($user->personal_facebook_account_from_left_measurement == null){
                $user->personal_facebook_account_from_left_measurement = '100';
            }
            if($user->personal_twitter_account_from_top_measurement == null){
                $user->personal_twitter_account_from_top_measurement = '310';
            }
            if($user->personal_twitter_account_from_left_measurement == null){
                $user->personal_twitter_account_from_left_measurement = '200';
            }
            if($user->personal_instagram_account_from_top_measurement == null){
                $user->personal_instagram_account_from_top_measurement = '320';
            }
            if($user->personal_instagram_account_from_left_measurement == null){
                $user->personal_instagram_account_from_left_measurement = '30';
            }

        return response()->json(new UserResource($user), 200);
    }


    public function updateUser(Request $request)
    {
        $user = User::find(auth('api')->user()->id);
        $rules = [
            '',
        ];
        $customMessages = [
            '',
        ];
        $validator = \Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        } else {
            $user->fill($request->all());
            $user->save();
            return response()->json(['message' => 'user updated successfully'], 200);
        }
    }

    public function updatePoliticalUser(Request $request)
    {
        $user = User::find(auth('api')->user()->id);
        // dd($user);
        $rules = [
            // 'political_personal_photo' => 'required|image|max:2048',
            // 'political_party_name' => 'string',
            'political_personal_name' => 'required|string',
            'political_designation' => 'required|string',
            'political_mobile' => 'nullable|digits:10',
            'political_email' => 'nullable|email',
            'political_website' => 'nullable|string',
            'political_facebook_account' => 'nullable|string',
            'political_twitter_account' => 'nullable|string',
            'political_instagram_account' => 'nullable|string',
        ];

        $customMessages = [
            // 'political_logo.image' => 'Political logo must be an image.',
            // 'political_logo.max' => 'Political logo may not be greater than 2048 kilobytes.',
            // 'political_logo.required' => 'Logo is required.',
            // 'political_personal_photo.required' => 'Photo is required.',
            // 'political_personal_photo.image' => 'Personal photo must be an image.',
            // 'political_personal_photo.max' => 'Political personal photo may not be greater than 2048 kilobytes.',
            'political_party_name.string' => 'Political party name must be a string.',
            'political_personal_name.required' => 'Political personal name is required.',
            'political_designation.required' => 'Political designation is required.',
            'political_mobile.required' => 'Political mobile is required.',
            'political_email.email' => 'Political email must be a valid email address.',
            'political_website.string' => 'Political website must be a string.',
            'political_facebook_account.string' => 'Political Facebook account must be a string.',
            'political_twitter_account.string' => 'Political Twitter account must be a string.',
            'political_instagram_account.string' => 'Political Instagram account must be a string.',
        ];

        if ($user->political_personal_photo == null) {
            $rules['political_personal_photo'] = 'required|image|max:2048';
            $customMessages['political_personal_photo.required'] = 'Photo is required.';
            $customMessages['political_personal_photo.image'] = 'Political logo must be an image.';
            $customMessages['political_personal_photo.max'] = 'Political logo may not be greater than 2048 kilobytes.';
        }
        // if ($user->political_logo == null) {
        //     $rules['political_logo'] = 'required|image|max:2048';
        //     $customMessages['political_logo.image'] = 'Political logo must be an image.';
        //     $customMessages['political_logo.max'] = 'Political logo may not be greater than 2048 kilobytes.';
        // }

        $validator = \Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user->fill($request->all());
        $user->is_profile_completed = 1;
        if($request->hasFile('political_personal_photo')){
            $user->political_personal_photo = \Storage::disk('public')->put('political_personal_photos',$request->political_personal_photo);
        }
        if($request->hasFile('political_logo')){
            $user->political_logo = \Storage::disk('public')->put('political_logos',$request->political_logo);
        }
        $user->save();
        return response()->json(['message' => 'user updated successfully','user' => new UserResource($user) ], 200);
    }

    public function updateBusinessUser(Request $request)
    {
        $user = User::find(auth('api')->user()->id);

        $rules = [
            // 'business_logo' => 'required|image|max:2048',
            // 'business_personal_photo' => 'nullable|image|max:2048',
            'business_category_name' => 'nullable|string',
            // 'business_company_name' => 'required|string',
            'business_personal_name' => 'nullable|string',
            'business_designation' => 'nullable|string',
            'business_mobile' => 'nullable|digits:10',
            'business_email' => 'nullable|email',
            'business_website' => 'nullable|string',
            'business_facebook_account' => 'nullable|string',
            'business_twitter_account' => 'nullable|string',
            'business_instagram_account' => 'nullable|string',
        ];

        $customMessages = [
            'business_logo.image' => 'Business logo must be an image.',
            'business_logo.max' => 'Business logo may not be greater than 2048 kilobytes.',
            'business_logo.required' => 'Logo is required.',
            'business_personal_photo.required' => 'Business personal photo is required.',
            'business_personal_photo.image' => 'Business personal photo must be an image.',
            'business_personal_photo.max' => 'Business personal photo may not be greater than 2048 kilobytes.',
            'business_category_name.string' => 'Business category name must be a string.',
            'business_company_name.string' => 'Business company name must be a string.',
            'business_personal_name.required' => 'Business personal name is required.',
            'business_designation.required' => 'Business designation is required.',
            'business_mobile.required' => 'Business mobile is required.',
            'business_email.email' => 'Business email must be a valid email address.',
            'business_website.string' => 'Business website must be a string.',
            'business_facebook_account.string' => 'Business Facebook account must be a string.',
            'business_twitter_account.string' => 'Business Twitter account must be a string.',
            'business_instagram_account.string' => 'Business Instagram account must be a string.',
        ];

        if ($user->business_personal_photo == null) {
            $rules['business_personal_photo'] = 'required|image|max:2048';
            $customMessages['business_personal_photo.required'] = 'Photo is required.';
            $customMessages['business_personal_photo.image'] = 'Business logo must be an image.';
            $customMessages['business_personal_photo.max'] = 'Business logo may not be greater than 2048 kilobytes.';
        }
        // if ($user->business_logo == null) {
        //     $rules['business_logo'] = 'required|image|max:2048';
        //     $customMessages['business_logo.image'] = 'Business logo must be an image.';
        //     $customMessages['business_logo.max'] = 'Business logo may not be greater than 2048 kilobytes.';
        // }


        $validator = \Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user->fill($request->all());
        $user->is_profile_completed = 1;
        if($request->hasFile('business_logo')){
            $user->business_logo = \Storage::disk('public')->put('business_logos',$request->business_logo);
        }
        if($request->hasFile('business_personal_photo')){
            $user->business_personal_photo = \Storage::disk('public')->put('business_personal_photos',$request->business_personal_photo);
        }
        $user->save();
        return response()->json(['message' => 'user updated successfully','user' => new UserResource($user) ], 200);
    }

    public function updatePersonalUser(Request $request)
    {
        $user = User::find(auth('api')->user()->id);

        $rules = [
            // 'personal_photo' => 'required|image|max:2048',
            'personal_name' => 'required|string',
            'personal_designation' => 'required|string',
            'personal_mobile' => 'nullable|digits:10',
            'personal_facebook_account' => 'nullable|string',
            'personal_twitter_account' => 'nullable|string',
            'personal_instagram_account' => 'nullable|string',
        ];

        $customMessages = [
            // 'personal_photo.image' => 'Personal photo must be an image.',
            // 'personal_photo.max' => 'Personal photo may not be greater than 2048 kilobytes.',
            'personal_name.required' => 'Personal name is required.',
            'personal_designation.required' => 'Personal designation is required.',
            'personal_mobile.required' => 'Personal mobile is required.',
            'personal_facebook_account.string' => 'Personal Facebook account must be a string.',
            'personal_twitter_account.string' => 'Personal Twitter account must be a string.',
            'personal_instagram_account.string' => 'Personal Instagram account must be a string.',
        ];

        if ($user->personal_photo == null) {
            $rules['personal_photo'] = 'required|image|max:2048';
            $customMessages['personal_photo.image'] = 'Personal photo must be an image.';
            $customMessages['personal_photo.max'] = 'Personal photo may not be greater than 2048 kilobytes.';
        }

        $validator = \Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user->fill($request->all());
        if($request->hasFile('personal_photo')){
            $user->personal_photo = \Storage::disk('public')->put('personal_photos',$request->personal_photo);
        }
        $user->is_profile_completed = 1;
        $user->save();
        return response()->json(['message' => 'user updated successfully','user' => new UserResource($user) ], 200);
    }

    public function myPackage()
    {
        $order = Order::where('user_id', auth('api')->user()->id)->where('status','COMPLETED')->orderby('id', 'DESC')->first();
        if ($order) {
            $package = Package::find($order->package_id);
            $package->days_remaining = $order->created_at->addDays($package->duration)->diffInDays(now());

            $valid_from = $order->created_at;
            $valid_to = $order->created_at->addDays($package->duration);
            $package->valid_from = $valid_from->format('d M Y');
            $package->valid_to = $valid_to->format('d M Y');

            $package = new PackageResource($package);
            return response()->json($package, 200);
        } else {
            return response()->json(['message' => 'No package found'], 404);
        }
    }

    public function createOrder(Request $request)
    {
        $order = new Order();
        $order->user_id = auth('api')->user()->id;
        $order->package_id = $request->package_id;
        $order->merchant_transaction_id = $request->merchant_transaction_id;
        $order->save();
        return response()->json(['message' => 'Order created successfully','order' => $order ], 200);
    }

    public function storePoster(Request $request)
    {
        $userposter = new Userposter();
        $userposter->user_id = auth('api')->user()->id;
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filePath = $file->store('userposters', 'public');
            $userposter->poster = $filePath;
        }
        $userposter->save();
        return response()->json(['message' => 'Poster created successfully','userposter' => $userposter ], 200);
    }

    public function getPosters(Request $request)
    {
        $userposters = Userposter::where('user_id',auth('api')->user()->id)->get();
        $sliders = new UserposterResource($userposters);
        return response()->json(['message' => 'Posters fetched successfully','userposters' => $userposters ], 200);
    }

    public function deletePosters(Request $request)
    {
        $userposter = Userposter::find($request->userposter_id);
        if($userposter){
            $userposter->delete();

            $userposters = Userposter::where('user_id',auth('api')->user()->id)->get();
            $sliders = new UserposterResource($userposters);
            return response()->json(['message' => 'Poster deleted successfully','userposters' => $userposters ], 200);
        }else{
            return response()->json(['message' => 'Poster not found' ], 404);
        }
    }

    function changeActiveProfile(Request $request){
        $user = User::find(auth('api')->user()->id);
        $user->active_profile = $request->active_profile;
        $user->save();
        return response()->json(['message' => 'Active profile changed successfully','user' => new UserResource($user) ], 200);
    }

    function  storeMeasurement(Request $request){
        $user = User::find(auth('api')->user()->id);

        $user->fill($request->all());
        $user->save();

        return response()->json(['message' => 'Measurement updated successfully','user' => new UserResource($user) ], 200);
    }



    //razorpay

    public function newOrder(Request $request)
    {
        $rules = [
            'service_id' => 'required|numeric|exists:services,id',
            'type' => 'required',
            'customer_addresses_id' => 'required|numeric|exists:customer_addresses,id',
            'final_amount' => 'required|numeric',
            'description' => 'required',
        ];

        $customMessages = [

            'service_id.exists' => 'Service not found',
            'service_id.required' => 'Service is required',
            'vendor_id.required' => 'Vendor is required',
            'type.required' => 'Type is required',
            'customer_addresses_id.required' => 'Address is required',
            'customer_addresses_id.exists' => 'Address not found',
            'final_amount.required' => ' amount is required',
            'final_amount.numeric' => 'Amount must be a number',
            'description.required' => 'Description is required',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user = User::find(auth('api')->user()->id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found',
            ], 404);
        }

        $customerAddress = CustomerAddress::where('id', $request->customer_addresses_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$customerAddress) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address does not belong to the customer.',
            ], 400);
        }

        if ($request->final_amount) {
            $amount = $request->final_amount;

            $api = new Api('rzp_test_j8Q0obZLYzn5CB', 'jfQgk4AcAhKe8slKpXi1xgPI');
            $razorpay_order = $api->order->create([
                'amount' => $amount * 100,  // Amount in paise
                'currency' => 'INR'
            ]);

            if ($razorpay_order) {
                $razorpay_order_id = $razorpay_order['id'];

                $order = new Order;
                $order->customer_id = $customer->id;
                $order->number = 'ORD-' . rand(1000, 9999);
                $order->service_id = $request->service_id;
                $order->type = $request->type;
                $order->customer_addresses_id = $request->customer_addresses_id;
                $order->final_amount = $amount;
                $order->description = $request->description;
                $order->vendor_id = $request->vendor_id;
                $order->razorpay_order_id = $razorpay_order_id;
                $order->save();

                $orderLog = new Orderlog();
                $orderLog->order_id = $order->id;
                $orderLog->title = 'Order Created Successfully';
                $orderLog->description = 'The order has been created successfully and is now being processed.';
                $orderLog->save();

                $data = [
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_api_key' => 'rzp_test_j8Q0obZLYzn5CB',
                    'currency' => 'INR',
                    'amount' => $order->final_amount,
                ];

                return response()->json($data)->setStatusCode(200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create Razorpay order.',
                ], 400);
            }
        } else {
            return response()->json(['message' => 'Please provide amount'], 400);
        }
    }


    public function paymentSuccess(Request $request)
    {
        $rules = [
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
        ];

        $customMessages = [
            'razorpay_order_id.required' => 'Order ID required',
            'razorpay_payment_id.required' => 'Payment ID required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        } else {
            $user = User::find(auth('api')->user()->id);
            $api = new Api('rzp_test_j8Q0obZLYzn5CB', 'jfQgk4AcAhKe8slKpXi1xgPI');
            try {
                $payment = $api->payment->fetch($request->razorpay_payment_id);
            } catch (\Exception $e) {
                return response()->json(['message' => 'payment ID given not correct,payment failed'], 400);
            }

            if ($payment) {
                $order = Order::where('razorpay_order_id', $request->razorpay_order_id)
                    ->with(['customer.user', 'vendor.user', 'customerAddress', 'service'])
                    ->first();
                if ($order) {
                    $order->status = 'COMPLETED';
                    $order->razorpay_payment_id = $request->razorpay_payment_id;
                    $order->save();

                    $orderLog = new Orderlog();
                    $orderLog->order_id = $order->id;
                    $orderLog->title = 'Order Payment Successful';
                    $orderLog->description = 'The payment has been processed successfully.';
                    $orderLog->save();

                    return response()->json([
                        'message' => 'payment success',
                      'order' => new MasterResource($order)
                    ]);
                } else {
                    return response()->json(['message' => 'order not found'], 400);
                }
            } else {
                return response()->json(['message' => 'payment failed'], 400);
            }
        }
    }
}

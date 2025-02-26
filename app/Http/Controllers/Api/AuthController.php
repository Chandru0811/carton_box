<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');
        $role = $request->input('role');

        $user = User::where('email', $credentials['email'])
            ->where('role', $role)->whereNull('deleted_at')->first();

        if ($user && Auth::attempt($credentials)) {
            $token = $user->createToken('Personal Access Token')->accessToken;
            $referreralCode = 'CBG500' . $user->id;

            // Handle cart assignment
            $cartnumber = $request->input('cartnumber') ?? session()->get('cartnumber');

            if ($cartnumber) {
                $guest_cart = Cart::where('cart_number', $cartnumber)->whereNull('customer_id')->first();

                if ($guest_cart) {
                    // Assign guest cart to the registered user
                    $guest_cart->customer_id = $user->id;
                    $guest_cart->save();

                    // Update session cartnumber
                    session(['cartnumber' => $guest_cart->cart_number]);
                }
            }

            $success['referrer_code'] = $referreralCode;
            $success['token'] = $token;
            $success['userDetails'] =  $user;
            $success['cartnumber'] = session('cartnumber');

            if ($user->role == 3) {
                $message = "Welcome {$user->name}, You have successfully logged in. Grab the latest DealsMachi offers now!";
            } else {
                $message = 'LoggedIn Successfully!';
            }

            return $this->success($message, $success);
        }

        return $this->error('Invalid email or password. Please check your credentials and try again.,Email.', ['error' => 'Invalid email or password. Please check your credentials and try again.,Email']);
    }



    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('role', $request->role);
                }),
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'referral_code' => $request->referral_code,
            'type' => $request->type
        ]);

        Auth::login($user);

        $referrerCode = 'CBG500' . $user->id;
        $token = $user->createToken('Personal Access Token')->accessToken;

        // Handle cart assignment
        $cartnumber = $request->input('cartnumber') ?? session()->get('cartnumber');

        if ($cartnumber) {
            $guest_cart = Cart::where('cart_number', $cartnumber)->whereNull('customer_id')->first();

            if ($guest_cart) {
                // Assign guest cart to the registered user
                $guest_cart->customer_id = $user->id;
                $guest_cart->save();

                // Update session cartnumber
                session(['cartnumber' => $guest_cart->cart_number]);
            }
        }

        $success['token'] = $token;
        $success['userDetails'] = $user;
        $success['referrer_code'] = $referrerCode;
        $success['cartnumber'] = session('cartnumber');

        return $this->success('Registered Successfully!', $success);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        return $this->ok('logged Out Successfully!');
    }

    public function forgetpassword(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists'   => 'The email does not exist.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        $username = $user->name;

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $resetLink = "https://dealsmachi.com/dealsmachiVendor/resetpassword?token=" . $token . "&email=" . urlencode($request->email);

        Mail::send('email.forgotPassword', ['resetLink' => $resetLink, 'name' => $username, 'token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['message' => 'We have e-mailed your password reset link!']);
    }

    public function resetpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            return response()->json(['message' => 'Invalid Token']);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return response()->json(['message' => 'Your password has been changed!']);
    }
}

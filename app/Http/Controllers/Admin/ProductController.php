<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Assign product to a user
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image|max:2048',
            'price' => 'required|numeric|min:0',
            'size' => 'required|string|max:255',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        $product = Product::create([
            'user_id' => $request->user_id,
            'image' => $imagePath,
            'price' => $request->price,
            'size' => $request->size,
        ]);

        return redirect()->back()->with('success', 'Product added to user successfully!');
    }

    // Show products for logged-in user
    public function userProducts()
    {
        $products = Product::where('user_id', Auth::id())->get();
        return view('user.products', compact('products'));
    }

    // Delete product
    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted.');
    }

    // Secure Acceptance Hosted Checkout
    public function pay(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $user = auth()->user();

        // Fields to sign - ALL fields sent to Cybersource must be included here
        $signedFieldNames = [
            'access_key',
            'profile_id',
            'transaction_uuid',
            'signed_field_names',
            'unsigned_field_names',
            'signed_date_time',
            'locale',
            'transaction_type',
            'reference_number',
            'amount',
            'currency',
            'merchant_category_code', // Required for sandbox
            'usd_outlet_id',          // Required for sandbox
            'usd_terminal_id',        // Required for sandbox
            'bill_to_forename',
            'bill_to_surname',
            'bill_to_email',
            'bill_to_address_line1',
            'bill_to_address_city',
            'bill_to_address_state',
            'bill_to_address_country',
            'bill_to_address_postal_code',
            'override_custom_receipt_page', // Redirect URL
        ];

        $params = [
            'profile_id' => env('CYBERSOURCE_PROFILE_ID'),
            'access_key' => env('CYBERSOURCE_ACCESS_KEY'),
            'transaction_uuid' => Str::uuid()->toString(),
            'signed_date_time' => gmdate("Y-m-d\TH:i:s\Z"),
            'locale' => 'en',
            'transaction_type' => 'sale',
            'reference_number' => 'ORDER-' . $product->id . '-' . time(),
            'amount' => number_format($product->price, 2, '.', ''),
            'currency' => 'USD',
            'merchant_category_code' => '0000', // Fixed value for sandbox testing
            'usd_outlet_id' => '12345', // Fixed value for sandbox testing
            'usd_terminal_id' => '67890', // Fixed value for sandbox testing
            'bill_to_forename' => $user->first_name ?? 'Test',
            'bill_to_surname' => $user->last_name ?? 'User',
            'bill_to_email' => $user->email ?? 'test@example.com',
            'bill_to_address_line1' => '1 Market St',
            'bill_to_address_city' => 'San Francisco',
            'bill_to_address_state' => 'CA',
            'bill_to_address_country' => 'US',
            'bill_to_address_postal_code' => '94105',
            'override_custom_receipt_page' => route('payment.response'),
            'signed_field_names' => implode(",", $signedFieldNames),
            'unsigned_field_names' => "",
        ];

        // Generate signature
        $params['signature'] = $this->sign($params, env('CYBERSOURCE_SECRET_KEY'));

        logger()->info('Cybersource payment params', $params);

        return view('payment.simple-checkout', [
            'product' => $product,
            'params' => $params,
            'action_url' => 'https://testsecureacceptance.cybersource.com/pay'
        ]);
    }

    private function sign($params, $secretKey)
    {
        $dataToSign = $this->buildDataToSign($params);
        logger()->info('Cybersource data_to_sign: ' . $dataToSign);
        return $this->generateSignature($dataToSign, $secretKey);
    }

    private function buildDataToSign($params)
    {
        $signedFieldNames = explode(",", $params["signed_field_names"]);
        $dataToSign = [];
        foreach ($signedFieldNames as $field) {
            $dataToSign[] = $field . "=" . $params[$field];
        }
        return implode(",", $dataToSign);
    }

    private function generateSignature($data, $secretKey)
    {
        return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
    }

    // Handle response from Cybersource
    public function paymentResponse(Request $request)
    {
        // Log all response data with detailed breakdown
        logger()->info('CyberSource Response - Full Details', [
            'all_data' => $request->all(),
            'decision' => $request->input('decision'),
            'reason_code' => $request->input('reason_code'),
            'message' => $request->input('message'),
            'auth_response' => $request->input('auth_response'),
            'request_id' => $request->input('req_reference_number'),
            'transaction_id' => $request->input('transaction_id'),
            'auth_code' => $request->input('auth_code'),
            'avs_code' => $request->input('req_avs_code'),
            'cv_code' => $request->input('req_cvv2_code'),
        ]);

        $decision = $request->input('decision');
        $reasonCode = $request->input('reason_code');
        $message = $request->input('message');
        $authResponse = $request->input('auth_response');
        $authCode = $request->input('auth_code');

        // Decode reason codes for better debugging
        $reasonCodeMeaning = $this->getReasonCodeMeaning($reasonCode);

        // Return view with all response data
        return view('payment.response', [
            'decision' => $decision,
            'reasonCode' => $reasonCode,
            'reasonCodeMeaning' => $reasonCodeMeaning,
            'message' => $message,
            'authResponse' => $authResponse,
            'authCode' => $authCode,
            'request' => $request
        ]);
    }

    private function getReasonCodeMeaning($reasonCode)
    {
        $reasonCodes = [
            '100' => 'Successful transaction',
            '101' => 'Missing one or more required fields',
            '102' => 'One or more fields in the request contains invalid data',
            '200' => 'The authorization request was approved',
            '201' => 'The issuing bank has questions about the request',
            '202' => 'Expired card',
            '203' => 'General decline of the card',
            '204' => 'Insufficient funds in the account',
            '205' => 'Stolen or lost card',
            '207' => 'Issuing bank unavailable',
            '208' => 'Inactive card or card not authorized for card-not-present transactions',
            '210' => 'The card has reached the credit limit',
            '211' => 'Invalid card verification number',
            '231' => 'Invalid merchant account number',
            '232' => 'The processor declined the request',
            '233' => 'General decline by the processor',
            '234' => 'There is a problem with your CyberSource merchant configuration',
            '236' => 'Processor failure',
            '240' => 'The card type is not accepted by the payment processor',
            '475' => 'The customer is enrolled in Payer Authentication',
            '476' => 'Payer Authentication was successful',
        ];

        return $reasonCodes[$reasonCode] ?? "Unknown reason code: $reasonCode";
    }
}

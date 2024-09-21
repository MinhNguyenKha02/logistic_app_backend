<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function payment(Request $request)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_HashSecret = "OHIIOQHLR4JO12AWZRVED1OWH771YI22";

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => "BL6NPHM0",
            "vnp_Amount" => 10000 * 100, // Convert to smallest unit
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(), // Get client IP
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Payment with " . ($request->input("order_id") ?? 'unknown'),
            "vnp_OrderType" => "other", // Check if "other" is valid
            "vnp_ReturnUrl" => "https://your-public-domain.com/api/vnpay-callback", // Use a valid URL
            "vnp_TxnRef" => uniqid(), // Ensure uniqueness
            "vnp_ExpireDate" => now()->addDays(30)->format('YmdHis'),
        ];

        ksort($inputData);
        $query = http_build_query($inputData);

        $vnp_Url .= "?" . $query;

        if ($vnp_HashSecret) {
            $hashString = urldecode(http_build_query($inputData));
            $vnpSecureHash = hash_hmac('sha512', $hashString, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        $returnData = [
            'code' => '00',
            'message' => 'success',
            'url' => $vnp_Url,
        ];

        return response()->json($returnData);
    }

    public function payCallBack(Request $request)
    {
        return response([
                        "bank_code"=>$request->input("vnp_BankCode"),
                        "bank_card_type"=>$request->input("vnp_CardType"),
                        "amount"=>$request->input("vnp_Amount"),
                        "status"=>$request->input("vnp_TransactionStatus"),
                        "transactions_id"=>$request->input("vnp_TransactionNo"),
                        "order_id"=>explode(" ",$request->input("vnp_OrderInfo"))[2],
                        "order_info"=>$request->input("vnp_OrderInfo"),
                        "payment_id"=>$request->input("vnp_TxnRef"),
                        "payment_date"=>\DateTime::createFromFormat('YmdHis', $request->input("vnp_PayDate"))->format('Y/m/d H:i:s'),
                        ],200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    private $title = 'label.transaction';
    private $icon = 'mdi mdi-account';
    private $dir = 'backend.transaction.';

    public function index()
    {
        return view($this->dir . 'index', [
            'title' => __($this->title),
            'icon' => $this->icon,
        ]);
    }

    public function dataTable()
    {
        $transaction = Transaction::select(['id', 'product_id', 'user_id', 'amount', 'status']);
        return DataTables::of($transaction)
            ->addIndexColumn()
            ->addColumn('product_name', function ($transaction) {
                return $transaction->product ? $transaction->product->name : '-';
            })
            ->addColumn('user_name', function ($transaction) {
                return $transaction->user ? $transaction->user->name : '-';
            })
            ->addColumn('status', function ($transaction) {
                return $transaction->status == 1 ? 'Success' : 'Pending';
            })
            ->addColumn('encrypted_id', function ($transaction) {
                return Crypt::encrypt($transaction->id);
            })
            ->make(true);
    }

    public function create()
    {



        $product = Product::all();
        return view($this->dir . 'create', [
            'title' => __('label.create') . ' ' . __($this->title),
            'icon' => $this->icon,
            'product' => $product,
        ]);
    }
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:product,id',
    //         'amount' => 'required|integer|min:1',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $product = Product::findOrFail($request->product_id);

    //         if ($product->quantity < $request->amount) {
    //             throw new \Exception('Insufficient stock');
    //         }

    //         $totalPrice = $product->price * $request->amount;

    //         $transaction = Transaction::create([
    //             'product_id' => $request->product_id,
    //             'user_id' => Auth::id(),
    //             'amount' => $totalPrice,
    //             'status' => 'success',
    //             'order_id' => 'ORD-' . Str::random(10),
    //             'payment_type' => 'qris',
    //             'transaction_time' => now(),
    //         ]);

    //         // Update product quantity
    //         $product->decrement('quantity', $request->amount);

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Transaction completed successfully',
    //             'data' => $transaction
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 422);
    //     }
    // }

    public function store(Request $request)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$clientKey = config('services.midtrans.client_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');

        $validated = $request->validate([
            'product_id' => 'required|exists:product,id',
            'amount' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            if ($product->quantity < $request->amount) {
                throw new \Exception('Insufficient stock');
            }

            $totalPrice = $product->price * $request->amount;

            $transaction = Transaction::create([
                'product_id' => $request->product_id,
                'user_id' => Auth::id(),
                'amount' => $totalPrice,
                'status' => 'pending',
                'order_id' => 'ORD-' . Str::random(10),
                'payment_type' => 'qris',
                'transaction_time' => now(),
            ]);

            $product->quantity -= $request->amount;
            $product->save();

            $payload = [
                'transaction_details' => [
                    'order_id' => $transaction->order_id,
                    'gross_amount' => $transaction->amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $request->amount,
                        'name' => $product->name,
                    ]
                ]
            ];

            // Create transaction and get the response
            $snapResponse = \Midtrans\Snap::createTransaction($payload);

            // Extract the snap token
            $snapToken = $snapResponse->token;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'data' => $transaction,
                'snap_url' => $snapResponse->redirect_url,
                'token' => $snapToken // Send snap token to frontend
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function handleCallback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $transaction = Transaction::where('order_id', $request->order_id)->first();
                if ($transaction) {
                    $transaction->update([
                        'status' => 'success',
                        'payment_type' => $request->payment_type,
                        'transaction_id' => $request->transaction_id,
                        'transaction_time' => $request->transaction_time
                    ]);

                    // Decrease product quantity
                    $product = $transaction->product;
                    $product->decrement('quantity', $transaction->amount / $product->price);
                }
            }
        }
        return response('OK', 200);
    }
}

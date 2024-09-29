<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $transaction = Transaction::with(['product', 'user'])
            ->select(['id', 'product_id', 'user_id', 'amount', 'status'])
            ->latest();

        return DataTables::of($transaction)
            ->addIndexColumn()
            ->addColumn('product_name', function ($transaction) {
                return $transaction->product ? $transaction->product->name : '-';
            })
            ->addColumn('user_name', function ($transaction) {
                return $transaction->user ? $transaction->user->name : '-';
            })
            ->addColumn('status', function ($transaction) {
                return $transaction->fresh()->status == 'success' ? 'Success' : 'Pending';
            })
            ->addColumn('encrypted_id', function ($transaction) {
                return Crypt::encrypt($transaction->id);
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function create()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();
        $latestTransaction = Transaction::where('user_id', Auth::id())
            ->latest()
            ->first();

        return view($this->dir . 'create', [
            'title' => __('label.create') . ' ' . __($this->title),
            'icon' => $this->icon,
            'products' => $products,
            'categories' => $categories,
            'latestTransaction' => $latestTransaction,
        ]);
    }

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
                ],
                'callbacks' => [
                    'finish' => route('transaction.create')
                ]
            ];

            $snapResponse = \Midtrans\Snap::createTransaction($payload);

            $snapToken = $snapResponse->token;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'data' => $transaction,
                'snap_url' => $snapResponse->redirect_url,
                'token' => $snapToken
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
            // $transaction->forceDelete();
            return response()->json(['success' => __('message.delete_success')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('message.delete_error')], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        Log::info('Midtrans Callback Received', $request->all());

        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            Log::info('Signature Verified');
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $transaction = Transaction::where('order_id', $request->order_id)->first();
                if ($transaction) {
                    Log::info('Updating transaction', ['order_id' => $request->order_id, 'new_status' => 'success']);
                    $transaction->update([
                        'status' => 'success',
                        'payment_type' => $request->payment_type,
                        'transaction_id' => $request->transaction_id,
                        'transaction_time' => $request->transaction_time
                    ]);
                    Log::info('Transaction updated successfully');
                } else {
                    Log::error('Transaction not found', ['order_id' => $request->order_id]);
                }
            } else {
                Log::info('Transaction status not capture or settlement', ['status' => $request->transaction_status]);
            }
        } else {
            Log::error('Invalid signature', ['received' => $request->signature_key, 'calculated' => $hashed]);
        }

        return response('OK', 200);
    }

    public function checkStatus($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->first();
        return response()->json(['status' => $transaction->status]);
    }

    public function updateStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        $transaction = Transaction::where('order_id', $orderId)->first();

        if ($transaction) {
            $transaction->status = 'success';
            $transaction->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SavedCart;
use App\Models\SavedCartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavedCartController extends Controller
{
    public function index(): JsonResponse
    {
        $savedCarts = SavedCart::where('user_id', 1) // TODO: Replace with auth()->id()
            ->with('items.product')
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($savedCarts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_name' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['nullable', 'in:Cash,Card,Credit'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.selling_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $savedCart = DB::transaction(function () use ($validated) {
                // Generate cart name if not provided
                $cartName = $validated['cart_name'] ?? $this->generateCartName();

                // Create saved cart
                $savedCart = SavedCart::create([
                    'user_id' => 1, // TODO: Replace with auth()->id()
                    'cart_name' => $cartName,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'payment_method' => $validated['payment_method'] ?? null,
                ]);

                // Create saved cart items
                foreach ($validated['items'] as $item) {
                    SavedCartItem::create([
                        'saved_cart_id' => $savedCart->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['selling_price'],
                        'tax' => $item['tax'],
                    ]);
                }

                return $savedCart->load('items.product');
            });

            return response()->json([
                'success' => true,
                'message' => 'Cart saved successfully',
                'savedCart' => $savedCart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save cart: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(SavedCart $savedCart): JsonResponse
    {
        $savedCart->load(['items.product.category', 'items.product.brand', 'items.stock']);

        return response()->json($savedCart);
    }

    public function destroy(SavedCart $savedCart): JsonResponse
    {
        try {
            // Check if user owns this cart
            if ($savedCart->user_id !== 1) { // TODO: Replace with auth()->id()
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $savedCart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cart: '.$e->getMessage(),
            ], 500);
        }
    }

    private function generateCartName(): string
    {
        $count = SavedCart::where('user_id', 1)->count() + 1; // TODO: Replace with auth()->id()

        return 'Cart-'.str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}

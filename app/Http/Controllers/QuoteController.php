<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Quote;
use App\Models\ThreeDModel;
use App\Services\QuoteService;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    protected QuoteService $quoteService;
    
    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }
    
    /**
     * Calculate a quote (AJAX endpoint).
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'three_d_model_id' => 'nullable|exists:three_d_models,id',
            'volume_mm3' => 'nullable|numeric|min:0',
            'material_id' => 'required|exists:materials,id',
            'color' => 'required|string',
            'quantity' => 'required|integer|min:1|max:100',
            'layer_height' => 'required|in:0.12,0.20,0.28',
            'infill' => 'required|integer|min:10|max:100',
            'shipping_enabled' => 'required|boolean',
            'country' => 'nullable|string|size:2',
        ]);
        
        try {
            $breakdown = $this->quoteService->calculate($validated);
            
            // Save quote if user is authenticated
            if (auth()->check()) {
                $quote = Quote::create([
                    'user_id' => auth()->id(),
                    'three_d_model_id' => $validated['three_d_model_id'] ?? null,
                    'configuration' => $validated,
                    'price_breakdown' => $breakdown,
                    'subtotal' => $breakdown['subtotal'],
                    'vat_amount' => $breakdown['vat_amount'],
                    'shipping_cost' => $breakdown['shipping_cost'],
                    'total' => $breakdown['total'],
                    'currency' => $breakdown['currency'],
                    'status' => 'draft',
                    'expires_at' => now()->addDays(7),
                ]);
                
                $breakdown['quote_id'] = $quote->id;
            }
            
            return response()->json([
                'success' => true,
                'breakdown' => $breakdown,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    
    /**
     * Show quote configurator page.
     */
    public function create(Request $request)
    {
        $modelId = $request->get('model_id');
        $model = null;
        
        if ($modelId) {
            $model = ThreeDModel::with('files')->findOrFail($modelId);
        }
        
        $materials = Material::active()->get();
        
        return view('quotes.create', compact('model', 'materials'));
    }
    
    /**
     * Store a quote (accept and create order).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote_id' => 'nullable|exists:quotes,id',
            'configuration' => 'required|array',
        ]);
        
        // If quote_id is provided, use that quote
        if (isset($validated['quote_id'])) {
            $quote = Quote::findOrFail($validated['quote_id']);
            $quote->update(['status' => 'accepted']);
        } else {
            // Calculate new quote
            $breakdown = $this->quoteService->calculate($validated['configuration']);
            
            $quote = Quote::create([
                'user_id' => auth()->id(),
                'three_d_model_id' => $validated['configuration']['three_d_model_id'] ?? null,
                'configuration' => $validated['configuration'],
                'price_breakdown' => $breakdown,
                'subtotal' => $breakdown['subtotal'],
                'vat_amount' => $breakdown['vat_amount'],
                'shipping_cost' => $breakdown['shipping_cost'],
                'total' => $breakdown['total'],
                'currency' => $breakdown['currency'],
                'status' => 'accepted',
                'expires_at' => now()->addDays(7),
            ]);
        }
        
        // Redirect to checkout
        return redirect()->route('checkout.create', ['quote_id' => $quote->id]);
    }
    
    /**
     * Show user's quotes.
     */
    public function index()
    {
        $quotes = Quote::with('threeDModel')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
        
        return view('quotes.index', compact('quotes'));
    }
    
    /**
     * Show a specific quote.
     */
    public function show(Quote $quote)
    {
        $this->authorize('view', $quote);
        
        return view('quotes.show', compact('quote'));
    }
}

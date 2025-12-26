<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Quotes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($quotes->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($quotes as $quote)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                @if($quote->threeDModel)
                                                    <a href="{{ route('models.show', $quote->threeDModel->slug) }}" class="hover:text-blue-600">
                                                        {{ $quote->threeDModel->title }}
                                                    </a>
                                                @else
                                                    Custom Print
                                                @endif
                                            </h3>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span>Created {{ $quote->created_at->diffForHumans() }}</span>
                                                @if($quote->expires_at)
                                                    <span>Expires {{ $quote->expires_at->diffForHumans() }}</span>
                                                @endif
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $quote->status === 'accepted' ? 'bg-green-100 text-green-800' : 
                                                       ($quote->status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($quote->status) }}
                                                </span>
                                            </div>

                                            <!-- Configuration Summary -->
                                            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                                                <div>
                                                    <span class="text-gray-500">Material:</span>
                                                    <span class="font-medium ml-1">
                                                        @if($quote->configuration['material_id'] ?? false)
                                                            {{ ['1' => 'PLA', '2' => 'PLA+', '3' => 'PETG', '4' => 'ABS', '5' => 'TPU', '6' => 'Nylon'][$quote->configuration['material_id']] ?? 'N/A' }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Color:</span>
                                                    <span class="font-medium ml-1">{{ $quote->configuration['color'] ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Quality:</span>
                                                    <span class="font-medium ml-1">{{ $quote->configuration['layer_height'] ?? 'N/A' }}mm</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Quantity:</span>
                                                    <span class="font-medium ml-1">{{ $quote->configuration['quantity'] ?? 1 }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="ml-4 text-right">
                                            <div class="text-2xl font-bold text-blue-600">
                                                €{{ number_format($quote->total, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                incl. VAT & shipping
                                            </div>
                                            @if($quote->status === 'draft')
                                                <a href="{{ route('quotes.show', $quote) }}" class="mt-2 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                                    View Details
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Price Breakdown (collapsible) -->
                                    <details class="mt-3">
                                        <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800">
                                            Show price breakdown
                                        </summary>
                                        <div class="mt-2 p-3 bg-gray-50 rounded text-sm space-y-1">
                                            <div class="flex justify-between">
                                                <span>Material Cost:</span>
                                                <span>€{{ number_format($quote->price_breakdown['breakdown']['material_cost'] ?? 0, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Machine Time:</span>
                                                <span>€{{ number_format($quote->price_breakdown['breakdown']['machine_cost'] ?? 0, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Setup Fee:</span>
                                                <span>€{{ number_format($quote->price_breakdown['breakdown']['setup_fee'] ?? 0, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between border-t pt-1">
                                                <span>Subtotal:</span>
                                                <span>€{{ number_format($quote->subtotal, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>VAT (24%):</span>
                                                <span>€{{ number_format($quote->vat_amount, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Shipping:</span>
                                                <span>€{{ number_format($quote->shipping_cost, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between font-bold border-t pt-1">
                                                <span>Total:</span>
                                                <span>€{{ number_format($quote->total, 2) }}</span>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $quotes->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No quotes yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Browse models and get instant quotes for 3D printing.</p>
                    <div class="mt-6">
                        <a href="{{ route('models.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Browse Models
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $model->title }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="modelPage">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Images & 3D Viewer -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- 3D Viewer Placeholder -->
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="mx-auto h-20 w-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">3D Viewer</p>
                                <p class="text-xs text-gray-400">Three.js viewer will be here</p>
                            </div>
                        </div>
                    </div>

                    <!-- Model Info -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $model->title }}</h3>
                        <p class="text-gray-600 mb-4">By {{ $model->user->name }}</p>

                        @if($model->description)
                            <div class="prose max-w-none mb-6">
                                <h4 class="font-semibold mb-2">Description</h4>
                                <p class="text-gray-700">{{ $model->description }}</p>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 py-4 border-t border-b">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($model->view_count) }}</div>
                                <div class="text-sm text-gray-500">Views</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($model->download_count) }}</div>
                                <div class="text-sm text-gray-500">Downloads</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($model->like_count) }}</div>
                                <div class="text-sm text-gray-500">Likes</div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="mt-6 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Category:</span>
                                <span class="font-medium">{{ $model->category->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">License:</span>
                                <span class="font-medium">{{ $model->license }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Published:</span>
                                <span class="font-medium">{{ $model->published_at ? $model->published_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Quote Configurator -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        <h3 class="text-lg font-bold mb-4">Get a Quote</h3>

                        @auth
                            <form @submit.prevent="calculateQuote">
                                <!-- Material -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                                    <select x-model="config.material_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="">Select material</option>
                                        <option value="1">PLA (Standard)</option>
                                        <option value="2">PLA+ (Premium)</option>
                                        <option value="3">PETG</option>
                                        <option value="4">ABS</option>
                                        <option value="5">TPU (Flexible)</option>
                                        <option value="6">Nylon</option>
                                    </select>
                                </div>

                                <!-- Color -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                    <select x-model="config.color" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="Black">Black</option>
                                        <option value="White">White</option>
                                        <option value="Red">Red</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Green">Green</option>
                                    </select>
                                </div>

                                <!-- Layer Height -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Layer Height</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button type="button" @click="config.layer_height = '0.12'" 
                                                :class="config.layer_height === '0.12' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                                class="px-3 py-2 rounded text-sm font-medium">0.12mm</button>
                                        <button type="button" @click="config.layer_height = '0.20'"
                                                :class="config.layer_height === '0.20' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                                class="px-3 py-2 rounded text-sm font-medium">0.20mm</button>
                                        <button type="button" @click="config.layer_height = '0.28'"
                                                :class="config.layer_height === '0.28' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                                class="px-3 py-2 rounded text-sm font-medium">0.28mm</button>
                                    </div>
                                </div>

                                <!-- Infill -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Infill: <span x-text="config.infill + '%'"></span>
                                    </label>
                                    <input type="range" x-model="config.infill" min="10" max="100" step="10" 
                                           class="w-full">
                                </div>

                                <!-- Quantity -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                    <input type="number" x-model="config.quantity" min="1" max="100" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                </div>

                                <!-- Calculate Button -->
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md font-medium mb-4" :disabled="calculating">
                                    <span x-show="!calculating">Calculate Quote</span>
                                    <span x-show="calculating">Calculating...</span>
                                </button>

                                <!-- Quote Result -->
                                <div x-show="quote" class="border-t pt-4">
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Material:</span>
                                            <span class="font-medium" x-text="'€' + (quote?.breakdown?.material_cost || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Machine Time:</span>
                                            <span class="font-medium" x-text="'€' + (quote?.breakdown?.machine_cost || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Setup Fee:</span>
                                            <span class="font-medium" x-text="'€' + (quote?.breakdown?.setup_fee || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between border-t pt-2">
                                            <span class="text-gray-600">Subtotal:</span>
                                            <span class="font-medium" x-text="'€' + (quote?.subtotal || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">VAT (24%):</span>
                                            <span class="font-medium" x-text="'€' + (quote?.vat_amount || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Shipping:</span>
                                            <span class="font-medium" x-text="'€' + (quote?.shipping_cost || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                                            <span>Total:</span>
                                            <span class="text-blue-600" x-text="'€' + (quote?.total || 0).toFixed(2)"></span>
                                        </div>
                                    </div>

                                    <button type="button" @click="proceedToCheckout" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-md font-medium mt-4">
                                        Proceed to Order
                                    </button>
                                </div>
                            </form>
                        @else
                            <p class="text-gray-600 mb-4">Please login to get a quote for this model.</p>
                            <a href="{{ route('login') }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md font-medium">
                                Login to Get Quote
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('modelPage', () => ({
                config: {
                    three_d_model_id: {{ $model->id }},
                    material_id: '',
                    color: 'Black',
                    quantity: 1,
                    layer_height: '0.20',
                    infill: 20,
                    shipping_enabled: true,
                    country: 'GR',
                    volume_mm3: 50000 // Default volume if metadata not available
                },
                quote: null,
                calculating: false,

                async calculateQuote() {
                    this.calculating = true;
                    try {
                        const response = await axios.post('{{ route('api.quotes.calculate') }}', this.config);
                        if (response.data.success) {
                            this.quote = response.data.breakdown;
                        }
                    } catch (error) {
                        console.error('Quote calculation error:', error);
                        alert(error.response?.data?.message || 'Failed to calculate quote');
                    } finally {
                        this.calculating = false;
                    }
                },

                proceedToCheckout() {
                    if (this.quote && this.quote.quote_id) {
                        window.location.href = '/quotes/' + this.quote.quote_id;
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>

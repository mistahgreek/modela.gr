<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Browse 3D Models') }}
            </h2>
            @auth
                <a href="{{ route('models.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Upload Model
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('models.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search models..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <select name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="downloads" {{ request('sort') == 'downloads' ? 'selected' : '' }}>Most Downloads</option>
                                <option value="likes" {{ request('sort') == 'likes' ? 'selected' : '' }}>Most Liked</option>
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                                Apply Filters
                            </button>
                            <a href="{{ route('models.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md text-sm font-medium inline-block">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Models Grid -->
            @if($models->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($models as $model)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Model Image -->
                            <a href="{{ route('models.show', $model->slug) }}" class="block">
                                <div class="aspect-square bg-gray-200 flex items-center justify-center">
                                    @if($model->primaryImage)
                                        <img src="{{ Storage::url($model->primaryImage->path) }}" 
                                             alt="{{ $model->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    @endif
                                </div>
                            </a>

                            <!-- Model Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-1 truncate">
                                    <a href="{{ route('models.show', $model->slug') }}" class="hover:text-blue-600">
                                        {{ $model->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">by {{ $model->user->name }}</p>
                                
                                <!-- Stats -->
                                <div class="flex items-center text-xs text-gray-500 space-x-3">
                                    <span title="Views">👁 {{ number_format($model->view_count) }}</span>
                                    <span title="Downloads">⬇ {{ number_format($model->download_count) }}</span>
                                    <span title="Likes">❤ {{ number_format($model->like_count) }}</span>
                                </div>

                                <!-- Price -->
                                <div class="mt-3">
                                    @if($model->price_type === 'free')
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                            Free
                                        </span>
                                    @elseif($model->price_type === 'paid')
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">
                                            €{{ number_format($model->model_price, 2) }}
                                        </span>
                                    @else
                                        <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">
                                            Print Service
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $models->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No models found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search terms.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

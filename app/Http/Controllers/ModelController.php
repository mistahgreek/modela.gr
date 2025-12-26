<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ThreeDModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModelController extends Controller
{
    /**
     * Display a listing of models.
     */
    public function index(Request $request)
    {
        $query = ThreeDModel::with(['user', 'category', 'primaryImage'])
            ->published();
        
        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'downloads':
                $query->orderBy('download_count', 'desc');
                break;
            case 'likes':
                $query->orderBy('like_count', 'desc');
                break;
            default:
                $query->latest('published_at');
        }
        
        $models = $query->paginate(24);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('models.index', compact('models', 'categories'));
    }
    
    /**
     * Show the form for creating a new model.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('models.create', compact('categories'));
    }
    
    /**
     * Store a newly created model.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price_type' => 'required|in:free,paid,print_only',
            'model_price' => 'nullable|numeric|min:0',
            'license' => 'required|string',
            'tags' => 'nullable|array',
            'visibility' => 'required|in:public,private,unlisted',
        ]);
        
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = 'draft';
        
        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (ThreeDModel::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }
        
        $model = ThreeDModel::create($validated);
        
        return redirect()->route('models.edit', $model)
            ->with('success', 'Model created successfully. Please upload files.');
    }
    
    /**
     * Display the specified model.
     */
    public function show(string $slug)
    {
        $model = ThreeDModel::with(['user', 'category', 'files', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Check permissions
        if (!$model->isPublished() && (!auth()->check() || auth()->id() !== $model->user_id)) {
            abort(403, 'This model is not available.');
        }
        
        // Increment view count
        $model->incrementViews();
        
        return view('models.show', compact('model'));
    }
    
    /**
     * Show the form for editing the specified model.
     */
    public function edit(ThreeDModel $model)
    {
        $this->authorize('update', $model);
        
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('models.edit', compact('model', 'categories'));
    }
    
    /**
     * Update the specified model.
     */
    public function update(Request $request, ThreeDModel $model)
    {
        $this->authorize('update', $model);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price_type' => 'required|in:free,paid,print_only',
            'model_price' => 'nullable|numeric|min:0',
            'license' => 'required|string',
            'tags' => 'nullable|array',
            'visibility' => 'required|in:public,private,unlisted',
            'status' => 'required|in:draft,published',
        ]);
        
        // Update slug if title changed
        if ($validated['title'] !== $model->title) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $count = 1;
            while (ThreeDModel::where('slug', $validated['slug'])->where('id', '!=', $model->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }
        
        // Set published_at if publishing
        if ($validated['status'] === 'published' && $model->status !== 'published') {
            $validated['published_at'] = now();
        }
        
        $model->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Model updated successfully',
                'model' => $model,
            ]);
        }
        
        return redirect()->route('models.show', $model->slug)
            ->with('success', 'Model updated successfully');
    }
    
    /**
     * Remove the specified model.
     */
    public function destroy(ThreeDModel $model)
    {
        $this->authorize('delete', $model);
        
        $model->delete();
        
        return redirect()->route('models.index')
            ->with('success', 'Model deleted successfully');
    }
    
    /**
     * My models listing.
     */
    public function myModels()
    {
        $models = ThreeDModel::with(['category', 'primaryImage'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(24);
        
        return view('models.my-models', compact('models'));
    }
}

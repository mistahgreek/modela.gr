# Implementation Guide - Modela.gr 3D Model Platform

## Overview

This guide documents the current state of the platform and provides detailed instructions for implementing the remaining features.

## What's Been Implemented ✅

### 1. Infrastructure & Setup
- **Docker Environment**: Full development stack with Nginx, PHP 8.3, MySQL 8, Redis, Mailpit
- **Laravel 11**: Clean installation with optimized configuration
- **Frontend Stack**: Tailwind CSS, Alpine.js, Axios configured and ready
- **Authentication**: Laravel Breeze with email verification and password reset
- **Stripe SDK**: Installed and configured
- **Build Tools**: Vite configured with HMR support

### 2. Database Schema (Complete)
All migrations created with proper indexes, foreign keys, and soft deletes:

- **users**: Extended with profile fields, admin flag, verification
- **categories**: For organizing 3D models
- **three_d_models**: Main model table with pricing, visibility, status
- **model_files**: STL/OBJ/3MF file metadata and processing status
- **model_images**: Model images with primary flag and ordering
- **materials**: PLA, PETG, ABS, TPU, Nylon with properties
- **printers**: Build volumes, nozzle sizes, hourly rates
- **pricing_rules**: Flexible rule engine for quote calculations
- **quotes**: Quote generation with configuration and breakdown
- **orders**: Full order lifecycle management
- **order_items**: Individual items with print settings
- **admin_settings**: Key-value configuration store

### 3. Eloquent Models (Complete)
All models include:
- Proper fillable fields and casts
- Relationship methods (hasMany, belongsTo, etc.)
- Helper methods (isPublished, incrementViews, etc.)
- Query scopes (active, published, search)
- Auto-generation (slugs, order numbers)

### 4. Demo Data (Complete)
Seeders created for:
- **8 Categories**: Art, Tools, Toys, Home, Fashion, Miniatures, Electronics, Educational
- **6 Materials**: PLA, PLA+, PETG, ABS, TPU, Nylon with realistic properties
- **3 Printers**: Prusa i3 MK3S+, Creality Ender 3 V2, Artillery Sidewinder X2
- **7 Pricing Rules**: Volume-based and material-specific rules
- **2 Users**: Admin and demo user accounts

### 5. Configuration
- **Platform Config** (`config/modela.php`): Upload limits, pricing factors, security settings
- **Environment**: Complete .env.example with all required variables
- **Stripe**: Keys configured for payments and webhooks

### 6. Documentation
- **README.md**: Complete setup guide for development and production
- **Deployment**: Step-by-step Ubuntu 22.04/24.04 deployment instructions
- **Systemd**: Queue worker service configuration
- **Nginx**: Production-ready configuration
- **SSL**: Certbot/Let's Encrypt setup

## What Needs to Be Implemented 🚧

### Phase 1: File Upload System

#### 1.1 Create Upload Controller
**File**: `app/Http/Controllers/ModelUploadController.php`

```php
class ModelUploadController extends Controller
{
    public function store(Request $request)
    {
        // Validate STL/OBJ/3MF files
        // Store file with unique name
        // Create ModelFile record with 'pending' status
        // Dispatch ProcessModelFile job
        // Return JSON with upload ID for polling
    }
    
    public function status($uploadId)
    {
        // Return processing status for AJAX polling
    }
}
```

#### 1.2 Create Processing Job
**File**: `app/Jobs/ProcessModelFile.php`

- Extract metadata (dimensions, volume, triangles)
- Generate checksum (SHA256)
- Update ModelFile with metadata
- Generate thumbnail (client-side screenshot or server render)
- Handle errors gracefully

#### 1.3 STL Parser Service
**File**: `app/Services/StlParserService.php`

- Read binary/ASCII STL format
- Calculate bounding box (min/max X, Y, Z)
- Calculate volume from mesh
- Count triangles
- Extract model statistics

#### 1.4 Upload UI Component
**File**: `resources/views/models/upload.blade.php`

- Drag-and-drop file upload
- Progress bar with Alpine.js
- File validation feedback
- Processing status updates via AJAX polling

### Phase 2: 3D Model Management

#### 2.1 Model CRUD Controller
**File**: `app/Http/Controllers/ModelController.php`

Methods needed:
- `index()`: Browse/search models
- `create()`: Upload form
- `store()`: Create model record
- `show($slug)`: Model detail page
- `edit($id)`: Edit form
- `update($id)`: Update model
- `destroy($id)`: Soft delete

#### 2.2 Model Detail Page
**File**: `resources/views/models/show.blade.php`

Components:
- Three.js viewer (load STL/OBJ/3MF)
- Model information (title, description, stats)
- Image gallery
- Download button (free models)
- Quote configurator (print-only/paid models)
- Like button (AJAX)
- View count tracking

#### 2.3 Three.js Viewer Component
**File**: `resources/js/components/ModelViewer.js`

```javascript
import * as THREE from 'three';
import { STLLoader } from 'three/addons/loaders/STLLoader.js';
import { OBJLoader } from 'three/addons/loaders/OBJLoader.js';

export default function modelViewer(fileUrl, format) {
    return {
        init() {
            // Setup Three.js scene, camera, renderer
            // Load model based on format
            // Add controls (OrbitControls)
            // Add lighting
            // Render loop
        }
    }
}
```

#### 2.4 Model Gallery
**File**: `resources/views/models/index.blade.php`

Features:
- Grid layout with Tailwind
- Filter by category (AJAX)
- Search with debounce
- Sort options (newest, popular, likes)
- Infinite scroll or pagination
- Model cards with primary image

### Phase 3: Quote Engine

#### 3.1 Quote Service
**File**: `app/Services/QuoteService.php`

```php
class QuoteService
{
    public function calculate(array $config): array
    {
        // Extract: model_id, material_id, color, quantity, layer_height, infill
        // Get model metadata (volume, weight estimate)
        // Find best pricing rule match
        // Calculate material cost = volume * density * waste_factor * price_per_gram
        // Estimate print time based on volume and layer height
        // Calculate machine cost = print_time * machine_rate
        // Apply setup fee
        // Apply margin
        // Ensure minimum price
        // Calculate VAT if enabled
        // Calculate shipping if enabled
        // Return breakdown array
    }
    
    public function findPricingRule(Material $material, float $volume): ?PricingRule
    {
        // Query active rules for material
        // Filter by volume range
        // Order by priority
        // Return first match or default
    }
}
```

#### 3.2 Quote Controller
**File**: `app/Http/Controllers/QuoteController.php`

```php
public function calculate(Request $request)
{
    // Validate inputs
    // Call QuoteService
    // Save Quote record
    // Return JSON breakdown for UI
}

public function store(Request $request)
{
    // Create accepted quote
    // Redirect to checkout
}
```

#### 3.3 Quote Configurator UI
**File**: `resources/views/components/quote-configurator.blade.php`

Alpine.js component with:
- Material selector (dropdown)
- Color picker
- Layer height radio (0.12/0.2/0.28)
- Infill slider (10-50%)
- Quantity input
- Live total update on change (debounced AJAX)
- Breakdown display (material, time, fees, VAT, shipping, total)
- "Add to Cart" button

### Phase 4: Stripe Integration

#### 4.1 Checkout Controller
**File**: `app/Http/Controllers/CheckoutController.php`

```php
public function createSession(Request $request)
{
    $quote = Quote::find($request->quote_id);
    
    // Create Order record (status: pending_payment)
    // Create OrderItems from quote
    // Create Stripe Checkout Session
    // Store stripe_session_id in order
    // Return redirect URL to Stripe
}
```

#### 4.2 Webhook Controller
**File**: `app/Http/Controllers/WebhookController.php`

```php
public function stripe(Request $request)
{
    // Verify Stripe signature
    // Handle events:
    //   - checkout.session.completed
    //   - payment_intent.succeeded
    //   - payment_intent.payment_failed
    // Update order status
    // Send confirmation email
    // Return 200 OK
}
```

#### 4.3 Stripe Service
**File**: `app/Services/StripeService.php`

```php
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripe;
    
    public function __construct()
    {
        $this->stripe = new StripeClient(config('modela.stripe.secret'));
    }
    
    public function createCheckoutSession(Order $order): string
    {
        // Build line items from order items
        // Set success/cancel URLs
        // Create session
        // Return session URL
    }
}
```

### Phase 5: Admin Panel

#### 5.1 Admin Middleware
**File**: `app/Http/Middleware/AdminMiddleware.php`

```php
public function handle($request, Closure $next)
{
    if (!auth()->user()?->isAdmin()) {
        abort(403, 'Unauthorized');
    }
    return $next($request);
}
```

Register in `bootstrap/app.php`

#### 5.2 Admin Routes
**File**: `routes/web.php`

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('materials', MaterialController::class);
    Route::resource('printers', PrinterController::class);
    Route::resource('pricing-rules', PricingRuleController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
});
```

#### 5.3 Admin Dashboard
**File**: `app/Http/Controllers/Admin/DashboardController.php`

Display stats:
- Total orders (today, week, month)
- Revenue (today, week, month)
- Pending orders count
- Models uploaded (today, total)
- Chart: Orders over time
- Chart: Revenue by material
- Recent orders table

#### 5.4 Material CRUD
**File**: `app/Http/Controllers/Admin/MaterialController.php`

Standard resource controller with inline AJAX editing:
- List with edit buttons
- Create modal (Alpine.js)
- Update via AJAX
- Delete confirmation
- Toggle active status

### Phase 6: AJAX & Interactive Features

#### 6.1 Toast Notification System
**File**: `resources/js/components/toasts.js`

```javascript
export default function toasts() {
    return {
        notifications: [],
        show(message, type = 'success') {
            const id = Date.now();
            this.notifications.push({ id, message, type });
            setTimeout(() => this.remove(id), 5000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }
}
```

#### 6.2 Modal Component
**File**: `resources/views/components/modal.blade.php`

Alpine.js modal with:
- Backdrop click to close
- ESC key to close
- Centered with animation
- Configurable size
- Slot for content

#### 6.3 Form Submission Helper
**File**: `resources/js/utils/ajax-form.js`

```javascript
export function submitForm(formEl, successCallback) {
    const formData = new FormData(formEl);
    
    return axios.post(formEl.action, formData)
        .then(response => {
            successCallback(response.data);
            window.toasts.show(response.data.message || 'Success!');
        })
        .catch(error => {
            if (error.response?.data?.errors) {
                // Display validation errors
            }
            window.toasts.show(error.response?.data?.message || 'Error', 'error');
        });
}
```

### Phase 7: Testing

#### 7.1 QuoteService Test
**File**: `tests/Unit/QuoteServiceTest.php`

```php
class QuoteServiceTest extends TestCase
{
    public function test_calculates_small_pla_part_correctly()
    {
        // Create material, pricing rule
        // Create mock model with known volume
        // Call QuoteService::calculate()
        // Assert price breakdown matches expected
    }
    
    public function test_applies_vat_when_enabled()
    {
        // Test VAT calculation
    }
    
    public function test_respects_minimum_price()
    {
        // Test minimum price enforcement
    }
}
```

#### 7.2 Feature Tests
- Upload flow
- Model creation
- Quote generation
- Stripe checkout
- Webhook handling
- Admin access control

### Phase 8: Security & Performance

#### 8.1 File Upload Validation
**File**: `app/Rules/ValidModelFile.php`

- Check MIME type
- Verify file extension
- Check file size
- Optional: Scan with ClamAV or similar
- Verify file is valid STL/OBJ/3MF

#### 8.2 Rate Limiting
**File**: `routes/api.php`

```php
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/upload', [ModelUploadController::class, 'store']);
});
```

#### 8.3 Signed URLs for Downloads
```php
// In ModelController
public function download($id)
{
    $model = ThreeDModel::findOrFail($id);
    
    // Check permissions
    // Generate signed URL
    return Storage::disk($model->files->first()->disk)
        ->temporaryUrl($model->files->first()->path, now()->addHour());
}
```

#### 8.4 Queue Worker
Ensure systemd service is running:
```bash
sudo systemctl status modela-queue
```

## Quick Start Development

1. **Start Docker**:
```bash
docker-compose up -d
```

2. **Run Migrations & Seed**:
```bash
docker-compose exec php php artisan migrate:fresh --seed
```

3. **Build Assets**:
```bash
npm run dev
```

4. **Access Application**:
- App: http://localhost:8080
- Login: admin@modela.gr / password

5. **Start Queue Worker** (in separate terminal):
```bash
docker-compose exec php php artisan queue:work
```

## Development Workflow

1. **Create Feature Branch**
2. **Implement Feature** (controller, service, view, tests)
3. **Test Locally**
4. **Run Linter**: `./vendor/bin/pint`
5. **Run Tests**: `php artisan test`
6. **Commit & Push**

## Deployment Checklist

Before deploying to production:

- [ ] Set `APP_DEBUG=false` in .env
- [ ] Configure real SMTP for emails
- [ ] Set up Stripe production keys
- [ ] Configure S3 storage (optional)
- [ ] Set up queue worker systemd service
- [ ] Configure cron for scheduler
- [ ] Set up database backups
- [ ] Configure SSL certificate
- [ ] Set up monitoring (Laravel Telescope, Sentry)
- [ ] Test Stripe webhooks with real endpoint
- [ ] Load test with realistic traffic

## Helpful Commands

```bash
# Create controller
php artisan make:controller ControllerName

# Create service
php artisan make:class Services/ServiceName

# Create job
php artisan make:job JobName

# Create request validation
php artisan make:request RequestName

# Create test
php artisan test --filter=TestName

# Clear caches
php artisan optimize:clear

# Generate IDE helper (development)
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
```

## Architecture Notes

### File Storage
- Public files: `storage/app/public/` (linked to `public/storage/`)
- Private files: `storage/app/private/`
- S3: Configure in `.env` and `config/filesystems.php`

### Job Queue
- Driver: Redis (configured in .env)
- Jobs: `app/Jobs/`
- Failed jobs table: already migrated
- Horizon: Consider adding for queue monitoring

### API Structure
- Public API: `/api/*` (for AJAX)
- Admin API: `/api/admin/*` (with admin middleware)
- Web routes: Standard Blade pages
- Mix: Blade pages with AJAX enhancements

### Database Optimization
- Indexes: Already added to migrations
- Eager loading: Use `with()` to avoid N+1 queries
- Pagination: Use `paginate()` instead of `get()`
- Cache: Store frequently accessed data (materials, categories)

## Common Issues & Solutions

### Upload Fails
- Check `upload_max_filesize` and `post_max_size` in PHP
- Verify storage permissions (775 for storage/)
- Check disk space

### Queue Not Processing
- Ensure Redis is running
- Check queue worker is running
- Check logs: `storage/logs/laravel.log`

### Stripe Webhooks Not Received
- Verify webhook URL is publicly accessible
- Check Stripe dashboard for failed webhooks
- Verify webhook secret matches .env

## Next Steps Priority

1. **Phase 1**: File Upload System (enables model uploads)
2. **Phase 3**: Quote Engine (core business logic)
3. **Phase 2**: 3D Model Management (user-facing features)
4. **Phase 4**: Stripe Integration (monetization)
5. **Phase 5**: Admin Panel (management)
6. **Phase 6**: Polish with AJAX/UX enhancements

---

**Last Updated**: 2025-12-26
**Platform Version**: 1.0.0-alpha
**Laravel Version**: 11.x

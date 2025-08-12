    <?php

    use App\Http\Controllers\CustomerController;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Controllers\UserController;
    use App\Http\Controllers\EventController;
    use App\Http\Controllers\PackageController;
    use App\Http\Controllers\CompanyController;
    use App\Http\Controllers\OrderController;
    use App\Http\Controllers\InventoryController;

    Route::get('/', function () {
        return view('auth.login');
    });

    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/register', function() {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can register new users.');
        }
        return view('auth.register');
    })->name('register');
    Route::post('/register', [UserController::class, 'register']);

    Route::get('/dashboard', function(){
        return view('dashboard');
    });

    Route::get('/order', function(){
        return view('orders.list');
    });

    Route::get('/ocr_system', function(){
        return view('ocr');
    });
    // Inventory Navigation (Public routes)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index'); // Main inventory page (all categories)
    Route::get('/inventory/category/{category}', [InventoryController::class, 'category'])->name('inventory.category'); // Category view
    Route::get('/inventory/company/{company}', [InventoryController::class, 'company'])->name('inventory.company'); // Filter by company
    Route::get('/inventory/slot/{slot}', [InventoryController::class, 'slot'])->name('inventory.slot'); // Slot grid view
    Route::get('/inventory/item/{item}', [InventoryController::class, 'item'])->name('inventory.item'); // Item detail view
    Route::get('/inventory/item/{item}/purchase', [InventoryController::class, 'purchaseForm'])->name('inventory.purchase.form'); // Purchase form
    Route::post('/inventory/item/{item}/purchase', [InventoryController::class, 'purchase'])->name('inventory.purchase'); // Handle purchase
    
    // Inventory Management Routes (Admin/Staff only)
    Route::middleware(['auth'])->group(function () {
        Route::get('/inventory/create', [InventoryController::class, 'createSlot'])->name('inventory.slot.create'); // Show add slot form
        Route::post('/inventory/store', [InventoryController::class, 'storeSlot'])->name('inventory.slot.store'); // Handle add slot
    });
   
    // Bulk purchase routes (admin/staff only)
    Route::middleware(['auth'])->group(function () {
        Route::get('/inventory/slot/{slot}/bulk-purchase', [InventoryController::class, 'bulkPurchaseForm'])->name('inventory.bulk-purchase.form'); // Show bulk purchase form
        Route::post('/inventory/slot/{slot}/bulk-purchase', [InventoryController::class, 'bulkPurchase'])->name('inventory.bulk-purchase'); // Handle bulk purchase
    });
   
    // User Management Routes - Available for both staff and agents
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin_staff', [UserController::class, 'adminStaff'])->name('adminStaff');
        Route::delete('/admin_staff/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Company Routes - Staff only
    Route::middleware(['auth'])->group(function () {
        Route::get('/company', [CompanyController::class, 'list'])->name('company.list');
        Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
        Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
        Route::get('/company/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('/company/{company}', [CompanyController::class, 'update'])->name('company.update');
        Route::delete('/company/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');
    });

    // Event Routes - Public access for viewing
    Route::get('/events/view', [EventController::class, 'eventsView'])->name('eventsView');
    Route::get('/events/view/{id}', [EventController::class, 'view'])->name('events.view');
    Route::get('/events/detail/{id}', [EventController::class, 'detail'])->name('events.detail');
    
    // Event Management Routes - Staff only
    Route::middleware(['auth'])->group(function () {
        Route::get('/events', [EventController::class, 'list'])->name('events');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    // Package Routes - Public access for viewing, Staff only for management
    Route::get('/packages/browse', [PackageController::class, 'browse'])->name('packages.browse');
    Route::get('/packages/compare', [PackageController::class, 'compare'])->name('packages.compare');
    Route::get('/packages/compare/add/{id}', [PackageController::class, 'addToCompare'])->name('packages.compare.add')->where('id', '[0-9]+');
    Route::get('/packages/compare/remove/{id}', [PackageController::class, 'removeFromCompare'])->name('packages.compare.remove');
    Route::get('/packages/compare/clear', [PackageController::class, 'clearCompare'])->name('packages.compare.clear');
    
    // Package Management Routes - Staff only
    Route::middleware(['auth'])->group(function () {
        Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
    });

    // Customer routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customer/details', [CustomerController::class, 'showDetailsForm'])->name('customer.details');
    Route::post('/customer/details/save', [CustomerController::class, 'saveDetails'])->name('customer.details.save');
    // Order routes
    Route::get('/order', [OrderController::class, 'index'])->name('orders.list');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/order/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/order/{id}', [OrderController::class, 'update'])->name('orders.update');
    
    // Customer Eternal Harmony Page (Public - No Authentication Required)
    Route::get('/eternal_harmony', [CustomerController::class, 'eternalHarmony'])->name('eternal.harmony');
    Route::post('/eternal_harmony/select-company', [CustomerController::class, 'selectCompany'])->name('eternal.harmony.select-company');
    Route::post('/eternal_harmony/search', [CustomerController::class, 'searchPurchasedSlots'])->name('eternal.harmony.search');
    Route::get('/inventory/item/{item}/edit-user', [InventoryController::class, 'editUserForm'])->name('inventory.item.edit-user');
    Route::post('/inventory/item/{item}/update-user', [InventoryController::class, 'updateUser'])->name('inventory.item.update-user');
    Route::get('/order/{order}/edit-users', [InventoryController::class, 'editOrderUsersForm'])->name('order.edit-users');
    Route::post('/order/{order}/update-users', [InventoryController::class, 'updateOrderUsers'])->name('order.update-users');
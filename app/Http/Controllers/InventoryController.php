<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    // Main inventory page: show all categories and slots
    public function index(Request $request)
    {
        $categories = ['columbarium', 'ancestor_pedestal', 'ancestral_tablet', 'burial_plot'];
        $companies = Company::all();
        $slots = DB::table('inventories')->get();
        return view('inventory.index', compact('categories', 'companies', 'slots'));
    }

    // Show slots by category
    public function category($category, Request $request)
    {
        $companies = Company::all();
        $query = DB::table('inventories')->where('category', $category);
        if ($request->has('company_id') && $request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        $slots = $query->get();
        return view('inventory.category', compact('category', 'companies', 'slots'));
    }

    // Show slots by company (optionally filter by category)
    public function company($companyId, Request $request)
    {
        $company = Company::findOrFail($companyId);
        $category = $request->get('category');
        $query = DB::table('inventories')->where('company_id', $companyId);
        if ($category) {
            $query->where('category', $category);
        }
        $slots = $query->get();
        return view('inventory.company', compact('company', 'slots', 'category'));
    }

    // Show seat map for a slot
    public function slot($slotId)
    {
        $slot = DB::table('inventories')->where('id', $slotId)->first();
        if (!$slot) {
            abort(404, 'Slot not found');
        }
        $items = DB::table('inventory_items')->where('inventory_id', $slotId)->get();
        return view('inventory.slot', compact('slot', 'items'));
    }



    // Show item detail (with image, purchase button)
    public function item($itemId)
    {
        $item = DB::table('inventory_items')->where('id', $itemId)->first();
        $slot = DB::table('inventories')->where('id', $item->inventory_id)->first();
        return view('inventory.item', compact('item', 'slot'));
    }

    // Show purchase form for an item
    public function purchaseForm($itemId)
    {
        $item = DB::table('inventory_items')->where('id', $itemId)->first();
        $slot = DB::table('inventories')->where('id', $item->inventory_id)->first();
        return view('inventory.purchase', compact('item', 'slot'));
    }

    // Handle purchase (insert customer info, choose payment)
    public function purchase(Request $request, $itemId)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'payment_method' => 'required|in:full_paid,installment',
            'installment_period' => 'nullable|integer|required_if:payment_method,installment',
        ]);

        // Find or create customer
        $customer = \App\Models\Customer::firstOrCreate(
            [
                'email' => $validated['email'],
            ],
            [
                'customer_name' => $validated['customer_name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
            ]
        );

        // Get item and slot info
        $item = \App\Models\InventoryItem::findOrFail($itemId);
        $slot = DB::table('inventories')->where('id', $item->inventory_id)->first();

        // Get correct row price
        $rowPrices = is_string($slot->row_prices) ? json_decode($slot->row_prices, true) : $slot->row_prices;
        $row = $item->row;
        $price = isset($rowPrices[$row - 1]) ? $rowPrices[$row - 1] : 0;
        $totalAmount = $price;
        $installmentDuration = null;
        $monthlyPayment = null;
        $paymentDetails = [
            'method' => $validated['payment_method'],
            'status' => 'pending',
        ];
        if ($validated['payment_method'] === 'installment') {
            $interestRates = [3 => 0.05, 6 => 0.08, 12 => 0.12];
            $interestRate = $interestRates[$validated['installment_period']] ?? 0;
            $totalAmount = $price * (1 + $interestRate);
            $installmentDuration = $validated['installment_period'];
            $monthlyPayment = $totalAmount / $validated['installment_period'];
            $paymentDetails['installment_period'] = $validated['installment_period'];
            $paymentDetails['monthly_payment'] = $monthlyPayment;
            $paymentDetails['total_amount'] = $totalAmount;
        }

        // Save order (using inventory_item_id for slot purchase)
        $order = \App\Models\Order::create([
            'customer_id' => $customer->id,
            'package_id' => null, // Not a package
            'inventory_item_id' => $item->id, // Reference the slot/item
            'user_id' => Auth::id(), // Add the current authenticated user
            'order_date' => now(),
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'installment_duration' => $installmentDuration,
            'monthly_payment' => $monthlyPayment,
            'total_amount' => $totalAmount,
            'receipt_details' => json_encode($paymentDetails),
            'package_status' => 'pending',
        ]);

        // Mark item as sold
        $item->status = 'sold';
        $item->save();

        return redirect()->route('inventory.index')->with('success', 'Purchase successful!');
    }

    // Show form to create a new slot
    public function createSlot()
    {
        // Only staff can create slots, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create inventory slots.');
        }

        $companies = \App\Models\Company::all();
        $categories = ['columbarium', 'ancestor_pedestal', 'ancestral_tablet', 'burial_plot'];
        
        if ($companies->count() === 0) {
            return redirect()->route('company.create')
                ->with('error', 'You need to create a company first before adding inventory slots.');
        }
        
        return view('inventory.create_slot', compact('companies', 'categories'));
    }

    // Handle storing a new slot and its items
    public function storeSlot(Request $request)
    {
        // Only staff can store slots, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create inventory slots.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'starting_slot_number' => 'required|integer|min:1',
                'company_id' => 'required|exists:companies,id',
                'category' => 'required|in:columbarium,ancestor_pedestal,ancestral_tablet,burial_plot',
                'columns' => 'required|integer|min:1',
                'row_group_counts' => 'required|array|min:1',
                'row_group_counts.*' => 'required|integer|min:1',
                'row_group_prices' => 'required|array|min:1',
                'row_group_prices.*' => 'required|numeric|min:0',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for slot creation: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Log the received data for debugging
        Log::info('Slot creation data received:', $validated);
        
        // Expand row groups into a flat array of row prices
        $rowPrices = [];
        $totalRows = 0;
        foreach ($validated['row_group_counts'] as $i => $count) {
            $price = $validated['row_group_prices'][$i];
            for ($j = 0; $j < $count; $j++) {
                $rowPrices[] = $price;
                $totalRows++;
            }
        }
        
        Log::info('Calculated row prices:', $rowPrices);
        Log::info('Total rows: ' . $totalRows);

        // Handle main image for the slot
        $mainImagePath = null;
        if ($request->hasFile('main_image')) {
            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_main_' . $mainImage->getClientOriginalName();
            $mainImage->move(public_path('images/inventory/slots'), $mainImageName);
            $mainImagePath = 'images/inventory/slots/' . $mainImageName;
        }

        // Handle additional images for the slot
        $additionalImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/inventory/slots'), $imageName);
                $additionalImages[] = 'images/inventory/slots/' . $imageName;
            }
        }

        // Create slot
        try {
            $slotData = [
                'name' => $validated['name'],
                'company_id' => $validated['company_id'],
                'category' => $validated['category'],
                'rows' => $totalRows,
                'columns' => $validated['columns'],
                'row_prices' => json_encode($rowPrices),
                'images' => !empty($additionalImages) ? json_encode($additionalImages) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            Log::info('Creating slot with data:', $slotData);
            
            $slotId = DB::table('inventories')->insertGetId($slotData);
            Log::info('Slot created with ID: ' . $slotId);
        } catch (\Exception $e) {
            Log::error('Failed to create slot: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create slot: ' . $e->getMessage())->withInput();
        }

        // Create items (cells) with custom slot numbering and images
        $items = [];
        $slotNumber = $validated['starting_slot_number'];
        for ($r = 1; $r <= $totalRows; $r++) {
            for ($c = 1; $c <= $validated['columns']; $c++) {
                $items[] = [
                    'inventory_id' => $slotId,
                    'row' => $r,
                    'column' => $c,
                    'slot_number' => $slotNumber, // Custom slot number
                    'status' => 'available',
                    'image' => $mainImagePath, // Main image from slot creation
                    'images' => !empty($additionalImages) ? json_encode($additionalImages) : null, // Additional images from slot creation
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $slotNumber++; // Increment slot number for next item
            }
        }
        try {
            Log::info('Creating ' . count($items) . ' inventory items');
            DB::table('inventory_items')->insert($items);
            Log::info('Inventory items created successfully');
            return redirect()->route('inventory.slot', $slotId)->with('success', 'Slot created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create inventory items: ' . $e->getMessage());
            // If item creation fails, delete the slot to maintain consistency
            DB::table('inventories')->where('id', $slotId)->delete();
            return redirect()->back()->with('error', 'Failed to create slot items: ' . $e->getMessage())->withInput();
        }
    }











    // Show bulk purchase form for multiple items
    public function bulkPurchaseForm($slotId, Request $request)
    {
        // Check if user is authenticated and is admin, staff, or agent
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'staff', 'agent'])) {
            abort(403, 'Only administrators, staff, and agents can perform bulk purchases.');
        }
        
        $slot = DB::table('inventories')->where('id', $slotId)->first();
        if (!$slot) {
            abort(404, 'Slot not found');
        }
        
        // Get selected item IDs from URL parameters
        $selectedItemIds = $request->get('items', []);
        
        // Get all available items for this slot
        $allItems = DB::table('inventory_items')
            ->where('inventory_id', $slotId)
            ->where('status', 'available')
            ->get();
        
        // Filter items to only show selected ones, or all if none selected
        if (!empty($selectedItemIds)) {
            $items = $allItems->whereIn('id', $selectedItemIds);
        } else {
            $items = $allItems;
        }
            
        return view('inventory.bulk_purchase', compact('slot', 'items'));
    }

    // Handle bulk purchase
    public function bulkPurchase(Request $request, $slotId)
    {
        // Check if user is authenticated and is admin, staff, or agent
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'staff', 'agent'])) {
            abort(403, 'Only administrators, staff, and agents can perform bulk purchases.');
        }
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'payment_method' => 'required|in:full_paid,installment',
            'installment_period' => 'nullable|integer|required_if:payment_method,installment',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'exists:inventory_items,id',
            'custom_prices' => 'required|array',
            'custom_prices.*' => 'required|numeric|min:0',
        ]);

        // Find or create customer
        $customer = \App\Models\Customer::firstOrCreate(
            [
                'email' => $validated['email'],
            ],
            [
                'customer_name' => $validated['customer_name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
            ]
        );

        $slot = DB::table('inventories')->where('id', $slotId)->first();
        $totalAmount = 0;
        $items = [];
        $itemDetails = [];

        // Process each selected item
        foreach ($validated['selected_items'] as $itemId) {
            $item = \App\Models\InventoryItem::findOrFail($itemId);
            
            // Check if item is still available
            if ($item->status !== 'available') {
                return redirect()->back()->with('error', 'One or more selected items are no longer available.');
            }

            // Use custom price for this purchase
            $customPrice = $validated['custom_prices'][$itemId] ?? 0;
            
            // Store item details for the order
            $itemDetails[] = [
                'item_id' => $item->id,
                'position' => $item->slot_number ?? ($item->row . '-' . $item->column),
                'custom_price' => $customPrice,
                'original_price' => $this->getItemOriginalPrice($item, $slot),
            ];

            if ($validated['payment_method'] === 'installment') {
                $interestRates = [3 => 0.05, 6 => 0.08, 12 => 0.12];
                $interestRate = $interestRates[$validated['installment_period']] ?? 0;
                $itemTotal = $customPrice * (1 + $interestRate);
                $totalAmount += $itemTotal;
            } else {
                $totalAmount += $customPrice;
            }

            // Mark item as sold
            $item->status = 'sold';
            $item->save();

            $items[] = $item;
        }

        // Calculate installment details
        $installmentDuration = null;
        $monthlyPayment = null;
        $paymentDetails = [
            'method' => $validated['payment_method'],
            'status' => 'pending',
            'items' => $itemDetails, // Store all items with their custom prices
            'total_items' => count($items),
        ];

        if ($validated['payment_method'] === 'installment') {
            $interestRates = [3 => 0.05, 6 => 0.08, 12 => 0.12];
            $interestRate = $interestRates[$validated['installment_period']] ?? 0;
            $installmentDuration = $validated['installment_period'];
            $monthlyPayment = $totalAmount / $validated['installment_period'];
            $paymentDetails['installment_period'] = $validated['installment_period'];
            $paymentDetails['monthly_payment'] = $monthlyPayment;
            $paymentDetails['total_amount'] = $totalAmount;
            $paymentDetails['interest_rate'] = $interestRate;
        }

        // Create single order for all items
        $order = \App\Models\Order::create([
            'customer_id' => $customer->id,
            'package_id' => null,
            'inventory_item_id' => $items[0]->id, // Store first item as primary reference
            'user_id' => Auth::id(), // Add the current authenticated user
            'order_date' => now(),
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'installment_duration' => $installmentDuration,
            'monthly_payment' => $monthlyPayment,
            'total_amount' => $totalAmount,
            'receipt_details' => json_encode($paymentDetails),
            'package_status' => 'pending',
        ]);

        // Attach all items to the order with their custom prices
        foreach ($itemDetails as $itemDetail) {
            $order->inventoryItems()->attach($itemDetail['item_id'], [
                'custom_price' => $itemDetail['custom_price'],
                'original_price' => $itemDetail['original_price'],
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Bulk purchase successful! ' . count($items) . ' items purchased in one order.');
    }

    // Helper method to get original price for an item
    private function getItemOriginalPrice($item, $slot)
    {
        $rowPrices = is_string($slot->row_prices) ? json_decode($slot->row_prices, true) : $slot->row_prices;
        $row = $item->row;
        return isset($rowPrices[$row - 1]) ? $rowPrices[$row - 1] : 0;
    }

    // Show form to edit user name for an item
    public function editUserForm($itemId)
    {
        $item = \App\Models\InventoryItem::findOrFail($itemId);
        $slot = DB::table('inventories')->where('id', $item->inventory_id)->first();
        return view('inventory.edit_user', compact('item', 'slot'));
    }

    // Handle updating user name for an item
    public function updateUser(Request $request, $itemId)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
        ]);

        $item = \App\Models\InventoryItem::findOrFail($itemId);
        $item->user_name = $validated['user_name'];
        $item->save();

        // Get the customer ID from the order that contains this item
        $order = \App\Models\Order::where('inventory_item_id', $itemId)
            ->orWhereHas('inventoryItems', function($query) use ($itemId) {
                $query->where('inventory_item_id', $itemId);
            })
            ->first();

        if ($order) {
            return redirect()->route('customers.show', $order->customer_id)->with('success', 'User name updated successfully!');
        }

        return redirect()->back()->with('success', 'User name updated successfully!');
    }

    // Show form to edit user names for all items in an order
    public function editOrderUsersForm($orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        $customer = $order->customer;
        
        // Get all inventory items for this order
        $items = collect();
        
        if ($order->isBulkPurchase()) {
            $items = $order->getAllInventoryItems();
        } else {
            $items = collect([$order->inventoryItem]);
        }
        
        return view('inventory.edit_order_users', compact('order', 'customer', 'items'));
    }

    // Handle updating user names for all items in an order
    public function updateOrderUsers(Request $request, $orderId)
    {
        $validated = $request->validate([
            'user_names' => 'required|array',
            'user_names.*' => 'required|string|max:255',
        ]);

        $order = \App\Models\Order::findOrFail($orderId);
        
        // Get all inventory items for this order
        $items = collect();
        
        if ($order->isBulkPurchase()) {
            $items = $order->getAllInventoryItems();
        } else {
            $items = collect([$order->inventoryItem]);
        }

        // Update each item with its corresponding user name
        foreach ($items as $index => $item) {
            if (isset($validated['user_names'][$item->id])) {
                $item->user_name = $validated['user_names'][$item->id];
                $item->save();
            }
        }

        return redirect()->route('customers.show', $order->customer_id)->with('success', 'User names updated successfully!');
    }
}

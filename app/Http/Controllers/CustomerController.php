<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Package;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function showDetailsForm(Request $request)
    {
        $package = Package::findOrFail($request->package_id);
        return view('packages.payment', compact('package'));
    }

    public function saveDetails(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'payment_method' => 'required|in:full_paid,installment',
            'installment_period' => 'nullable|integer|required_if:payment_method,installment',
        ]);

        // Debug logging
        Log::info('Customer details received:', $validated);

        $customer = Customer::create([
            'customer_name' => $validated['customer_name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'address' => $validated['address'],
        ]);

        $package = Package::find($validated['package_id']);

        $paymentDetails = [
            'method' => $validated['payment_method'],
            'status' => 'pending',
        ];

        $totalAmount = $package->price;
        $installmentDuration = null;
        $monthlyPayment = null;

        if ($validated['payment_method'] === 'installment') {
            $interestRates = [3 => 0.05, 6 => 0.08, 12 => 0.12];
            $interestRate = $interestRates[$validated['installment_period']];
            $totalAmount = $package->price * (1 + $interestRate);
            $installmentDuration = $validated['installment_period'];
            $monthlyPayment = $totalAmount / $validated['installment_period'];
            
            $paymentDetails['installment_period'] = $validated['installment_period'];
            $paymentDetails['monthly_payment'] = $monthlyPayment;
            $paymentDetails['total_amount'] = $totalAmount;
        }

        Log::info('Payment Method: ' . $validated['payment_method']);
        Log::info('Installment Period: ' . ($validated['installment_period'] ?? 'N/A'));
        Log::info('Total Amount: ' . $totalAmount);
        Log::info('Installment Duration: ' . ($installmentDuration ?? 'N/A'));
        Log::info('Monthly Payment: ' . ($monthlyPayment ?? 'N/A'));

        $orderData = [
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'user_id' => Auth::id(), // Add the current authenticated user (agent)
            'order_date' => now(),
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'installment_duration' => $installmentDuration,
            'monthly_payment' => $monthlyPayment,
            'total_amount' => $totalAmount,
            'receipt_details' => json_encode($paymentDetails),
            'package_status' => 'pending',
        ];

        Log::info('Order data to be created:', $orderData);

        $order = Order::create($orderData);

        Log::info('Order created successfully with ID: ' . $order->id);

        // Clear comparison session after successful order
        session()->forget('compare');

        return redirect()->route('packages.compare')->with('success', 'Order placed successfully!');
    }

    public function index(Request $request)
    {
        // Get all customers
        $allCustomers = Customer::with([
            'orders' => function($query) {
                $query->orderBy('order_date', 'desc');
            }, 
            'orders.package.company',
            'orders.inventoryItem.inventory',
            'orders.inventoryItems.inventory'
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $allCustomers->where(function($q) use ($searchTerm) {
                $q->where('customer_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('address', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $allCustomers->whereHas('orders.package', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })->orWhereHas('orders.inventoryItem.inventory', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        // Filter by order type
        if ($request->filled('order_type')) {
            if ($request->order_type === 'packages') {
                $allCustomers->whereHas('orders', function($q) {
                    $q->whereNotNull('package_id');
                });
            } elseif ($request->order_type === 'slots') {
                $allCustomers->whereHas('orders', function($q) {
                    $q->whereNotNull('inventory_item_id');
                });
            }
        }

        $allCustomers = $allCustomers->get();

        // Group customers by deduplication rule
        $groups = [];
        foreach ($allCustomers as $customer) {
            $found = false;
            foreach ($groups as &$group) {
                $matchCount = 0;
                if ($group['name'] === $customer->customer_name) $matchCount++;
                if ($group['email'] === $customer->email) $matchCount++;
                if ($group['phone'] === $customer->phone_number) $matchCount++;
                if ($matchCount >= 2) {
                    $group['customers'][] = $customer;
                    $group['orders'] = $group['orders']->merge($customer->orders);
                    $group['package_orders'] = $group['orders']->whereNotNull('package_id');
                    $group['slot_orders'] = $group['orders']->whereNotNull('inventory_item_id');
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $groups[] = [
                    'name' => $customer->customer_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone_number,
                    'customers' => collect([$customer]),
                    'orders' => $customer->orders,
                    'package_orders' => $customer->orders->whereNotNull('package_id'),
                    'slot_orders' => $customer->orders->whereNotNull('inventory_item_id'),
                    'id' => $customer->id, // Use the first customer's id for detail link
                ];
            }
        }

        // Limit orders to newest 2 for each group
        foreach ($groups as &$group) {
            $group['orders'] = $group['orders']->sortByDesc('order_date')->take(2);
        }

        // Convert to a collection for pagination compatibility
        $groupedCustomers = collect($groups);
        // Simple pagination (10 per page)
        $perPage = 10;
        $page = request()->get('page', 1);
        $paged = $groupedCustomers->slice(($page - 1) * $perPage, $perPage)->values();
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged,
            $groupedCustomers->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $companies = Company::all();
        return view('customers', compact('customers', 'companies'));
    }

    public function show($id)
    {
        // Load the primary customer first
        $primaryCustomer = Customer::findOrFail($id);

        // Find potential duplicate customer records (same name/email/phone)
        // We'll merge orders across records that match at least 2 of these 3 fields
        $potentialMatches = Customer::where(function($q) use ($primaryCustomer) {
                $q->where('customer_name', $primaryCustomer->customer_name)
                  ->orWhere('email', $primaryCustomer->email)
                  ->orWhere('phone_number', $primaryCustomer->phone_number);
            })
            ->with([
                'orders.package.company',
                'orders.inventoryItem.inventory.company',
                'orders.inventoryItems.inventory.company',
                'orders' => function($query) {
                    $query->orderBy('order_date', 'desc');
                }
            ])
            ->get();

        // Group by dedup rule (>= 2 matching fields)
        $matchingCustomers = $potentialMatches->filter(function($other) use ($primaryCustomer) {
            $matches = 0;
            if ($other->customer_name === $primaryCustomer->customer_name) $matches++;
            if ($other->email === $primaryCustomer->email) $matches++;
            if ($other->phone_number === $primaryCustomer->phone_number) $matches++;
            return $matches >= 2;
        });

        // Ensure the primary customer is included
        if (!$matchingCustomers->contains('id', $primaryCustomer->id)) {
            $matchingCustomers->push($primaryCustomer->load([
                'orders.package.company',
                'orders.inventoryItem.inventory.company',
                'orders.inventoryItems.inventory.company',
                'orders' => function($query) {
                    $query->orderBy('order_date', 'desc');
                }
            ]));
        }

        // Merge orders across all matching customers
        $mergedOrders = $matchingCustomers->flatMap(function($cust) {
            return $cust->orders;
        })->sortByDesc('order_date')->values();

        // Build the display data
        $package_orders = $mergedOrders->whereNotNull('package_id');
        $slot_orders = $mergedOrders->filter(function($order) {
            if ($order->inventory_item_id !== null) {
                return true;
            }
            if ($order->receipt_details && isset($order->receipt_details['items'])) {
                return count($order->receipt_details['items']) > 0;
            }
            return $order->inventoryItems->isNotEmpty();
        });

        // For the header, keep showing the primary customer's identity
        $customer = $primaryCustomer;

        return view('customer.show', compact('customer', 'package_orders', 'slot_orders'));
    }

    public function eternalHarmony()
    {
        $companies = Company::all();
        $selectedCompany = null;
        return view('eternal_harmony', compact('companies', 'selectedCompany'));
    }

    public function searchPurchasedSlots(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'user_name' => 'required|string|max:255',
            ]);

            $company = Company::findOrFail($validated['company_id']);
            
            // Search for inventory items with matching user name
            $inventoryItems = \App\Models\InventoryItem::where('user_name', 'LIKE', '%' . $validated['user_name'] . '%')
                ->whereHas('inventory', function($query) use ($validated) {
                    $query->where('company_id', $validated['company_id']);
                })
                ->with(['inventory.company'])
                ->get();

            Log::info('Found inventory items: ' . $inventoryItems->count());

            $purchasedSlots = [];
            
            foreach ($inventoryItems as $item) {
                Log::info('Found item - Slot number: ' . ($item->slot_number ?? 'NULL') . ', User: ' . ($item->user_name ?? 'NULL'));
                $purchasedSlots[] = [
                    'user_name' => $item->user_name,
                    'slot_number' => $item->slot_number,
                ];
            }

            $companies = Company::all();
            
            Log::info('Purchased slots data:', $purchasedSlots);
            
            return view('eternal_harmony', compact('companies', 'purchasedSlots', 'company', 'validated'));
        } catch (\Exception $e) {
            Log::error('Error searching purchased slots: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while searching. Please try again.');
        }
    }

    public function selectCompany(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $companies = Company::all();
        
        return view('eternal_harmony', compact('companies', 'company'));
    }
}
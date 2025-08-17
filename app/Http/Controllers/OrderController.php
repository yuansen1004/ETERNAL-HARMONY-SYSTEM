<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'package', 'inventoryItem.inventory', 'inventoryItems.inventory']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('customer', function($customerQuery) use ($searchTerm) {
                      $customerQuery->where('customer_name', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('package', function($packageQuery) use ($searchTerm) {
                      $packageQuery->where('package_name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('inventoryItem.inventory', function($inventoryQuery) use ($searchTerm) {
                      $inventoryQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }



        // Existing filters
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }
        if ($request->filled('status')) {
            $query->where('package_status', $request->status);
        }
        if ($request->filled('inventory_item_id')) {
            $query->where('inventory_item_id', $request->inventory_item_id);
        }
        if ($request->filled('inventory_id')) {
            $query->whereHas('inventoryItem', function($q) use ($request) {
                $q->where('inventory_id', $request->inventory_id);
            });
        }
        if ($request->filled('category')) {
            $query->whereHas('inventoryItem', function($q) use ($request) {
                $q->whereHas('inventory', function($q2) use ($request) {
                    $q2->where('category', $request->category);
                });
            });
        }
        if ($request->filled('company_id')) {
            $query->whereHas('package', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })->orWhereHas('inventoryItem.inventory', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $orders = $query->latest()->paginate(10)->appends($request->all());
        $packages = \App\Models\Package::all();
        $categories = ['columbarium', 'ancestor_pedestal', 'ancestral_tablet', 'burial_plot'];
        $companies = \App\Models\Company::all();
        return view('orders.list', compact('orders', 'packages', 'categories', 'companies'));
    }

    public function show($id)
    {
        $order = \App\Models\Order::with(['customer', 'package', 'inventoryItem.inventory', 'inventoryItems.inventory'])->findOrFail($id);
        return view('order', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with(['customer', 'package', 'inventoryItem.inventory', 'inventoryItems.inventory'])->findOrFail($id);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $user = Auth::user();

        $validated = $request->validate([
            'installment_paid' => 'nullable|integer|min:0',
            'package_status' => 'required|in:pending,complete',
            'payment_progress' => 'nullable|boolean',
            'user_names' => 'nullable|array',
            'user_names.*' => 'nullable|string|max:255',
        ]);

        // Handle payment progress updates based on payment method
        if ($order->payment_method === 'full_paid') {
            // For full payment orders
            if (isset($validated['payment_progress'])) {
                if ($user->role === 'agent' && $order->payment_progress) {
                    // Agent cannot change payment progress if it's already marked as complete
                    return redirect()->back()->with('error', 'Agents cannot modify payment progress once it has been marked as complete.');
                }
                
                // Staff and admin can always modify payment progress
                $order->payment_progress = $validated['payment_progress'];
                $order->installment_paid = $validated['payment_progress'] ? 1 : 0;
            }
        } else if ($order->payment_method === 'installment') {
            // For installment orders
            if (isset($validated['installment_paid'])) {
                $newInstallmentPaid = $validated['installment_paid'];
                
                // Check if agent is trying to decrease installment progress
                if ($user->role === 'agent' && $newInstallmentPaid < $order->installment_paid) {
                    return redirect()->back()->with('error', 'Agents cannot decrease installment progress once payments are marked as complete.');
                }
                
                // Check if agent is trying to modify already completed installments
                if ($user->role === 'agent' && $order->installment_paid > 0) {
                    // Allow agents to only increase progress, not modify existing ones
                    if ($newInstallmentPaid < $order->installment_paid) {
                        return redirect()->back()->with('error', 'Agents cannot modify completed installment payments.');
                    }
                }
                
                $order->installment_paid = $newInstallmentPaid;
                
                // Update payment_progress based on installment completion
                if ($newInstallmentPaid >= $order->installment_duration) {
                    $order->payment_progress = true;
                } else {
                    $order->payment_progress = false;
                }
            }
        }

        $order->package_status = $validated['package_status'];
        $order->save();

        // Update user names for slot orders
        if (!$order->package && isset($validated['user_names'])) {
            if ($order->isBulkPurchase()) {
                $items = $order->getAllInventoryItems();
            } else {
                $items = collect([$order->inventoryItem]);
            }

            foreach ($items as $item) {
                if (isset($validated['user_names'][$item->id])) {
                    $item->user_name = $validated['user_names'][$item->id];
                    $item->save();
                }
            }
        }

        return redirect()->route('orders.list')->with('success', 'Order updated successfully!');
    }

    public function exportToPdf($id)
    {
        $order = Order::with(['customer', 'package', 'inventoryItem.inventory', 'inventoryItems.inventory', 'user'])->findOrFail($id);
        
        // Generate filename
        $orderType = $order->package ? 'package' : 'inventory';
        $agentName = $order->user ? str_replace(' ', '-', $order->user->name) : 'unknown';
        $date = $order->order_date->format('Y-m-d');
        $filename = "Order-{$orderType}-{$agentName}-{$date}.pdf";
        
        // Prepare data for PDF
        $data = [
            'order' => $order,
            'orderType' => $orderType,
            'agentName' => $order->user ? $order->user->name : 'Unknown Agent',
            'currentDate' => now()->format('d M Y'),
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('orders.pdf', $data);
        
        // Return PDF as download
        return $pdf->download($filename);
    }
}
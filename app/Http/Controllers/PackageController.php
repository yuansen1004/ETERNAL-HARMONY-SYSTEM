<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Company;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        // Only staff can view package list, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can access package list.');
        }

        // Clear comparison when users navigate to main packages page
        if ($request->has('clear_compare')) {
            session()->forget('compare');
        }
        
        $companies = Company::all();
        $query = Package::with('company');

        if ($request->has('company_id') && !empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        $packages = $query->paginate(10);
        return view('packages.index', compact('packages', 'companies'));
    }

    public function create()
    {
        // Only staff can create packages, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create packages.');
        }

        $companies = Company::all();
        return view('packages.create', compact('companies'));
    }

    public function store(Request $request)
    {
        // Only staff can store packages, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create packages.');
        }

        $validatedData = $request->validate([
            'package_name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'company_id' => 'required|exists:companies,id',
        ]);

        try {
            Package::create($validatedData);
            return redirect()->route('packages.index')->with('success', 'Package created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating package: ' . $e->getMessage());
        }
    }

    public function browse(Request $request)
    {
        // Clear comparison only if explicitly requested
        if ($request->has('clear_compare')) {
            session()->forget('compare');
        }
        
        $companies = Company::all();
        $query = Package::with('company');

        if ($request->has('company_id') && $request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('price_range') && $request->price_range) {
            if ($request->price_range === '1000+') {
                $query->where('price', '>', 1000);
            } else {
                $range = explode('-', $request->price_range);
                if (count($range) === 2) {
                    $query->whereBetween('price', [(float)$range[0], (float)$range[1]]);
                }
            }
        }

        $packages = $query->paginate(12);
        return view('packages.browse', compact('packages', 'companies'));
    }

    public function clearCompare()
    {
        session()->forget('compare');
        return redirect()->route('packages.browse')
            ->with('success', 'Comparison list cleared successfully.');
    }

    public function edit(Package $package)
    {
        // Only staff can edit packages, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can edit packages.');
        }

        $companies = Company::all();
        return view('packages.edit', compact('package', 'companies'));
    }

    public function update(Request $request, Package $package)
    {
        // Only staff can update packages, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can update packages.');
        }

        $validatedData = $request->validate([
            'package_name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'company_id' => 'required|exists:companies,id',
        ]);

        $package->update($validatedData);
        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        // Only staff can delete packages, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can delete packages.');
        }

        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully.');
    }

    public function compare()
    {
        $compareIds = Session::get('compare', []);
        $packages = Package::with('company')->whereIn('id', $compareIds)->get();
        
        return view('packages.compare', compact('packages'));
    }

    public function addToCompare($id)
    {
        $package = Package::findOrFail($id);
        
        $compare = session()->get('compare', []);
        
        // Check if package is already in comparison
        if (in_array($id, $compare)) {
            return redirect()->route('packages.compare')
                ->with('error', 'This package is already in your comparison list.');
        }
        
        // Add to compare
            $compare[] = $id;
            
            // Keep only last 2 packages
            if (count($compare) > 2) {
                array_shift($compare);
            }
            
            session()->put('compare', $compare);

        return redirect()->route('packages.compare')
            ->with('success', 'Package "' . $package->package_name . '" added to comparison');
    }

    public function removeFromCompare($id)
    {
        $compare = Session::get('compare', []);
        
        if (($key = array_search($id, $compare)) !== false) {
            unset($compare[$key]);
            Session::put('compare', array_values($compare));
        }

        return redirect()->route('packages.compare')
            ->with('success', 'Package removed from comparison');
    }

    public function show(Package $package)
    {
        return redirect()->route('packages.compare.add', $package->id);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function list()
    {
        // Only staff can view company list, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can access company management.');
        }

        $companies = Company::all();
        return view('company.list', compact('companies'));
    }

    public function create()
    {
        // Only staff can create companies, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create companies.');
        }

        return view('company.create');
    }

    public function store(Request $request)
    {
        // Only staff can store companies, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can create companies.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:companies,contact_email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        Company::create($request->all());
        return redirect()->route('company.list')->with('success', 'Company created successfully!');
    }

    public function edit(Company $company)
    {
        // Only staff can edit companies, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can edit companies.');
        }

        return view('company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        // Only staff can update companies, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can update companies.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:companies,contact_email,' . $company->id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $company->update($request->all());
        return redirect()->route('company.list')->with('success', 'Company updated successfully!');
    }

    public function destroy(Company $company)
    {
        // Only staff can delete companies, not agents
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can delete companies.');
        }

        $company->delete();
        return redirect()->route('company.list')->with('success', 'Company deleted successfully!');
    }
}
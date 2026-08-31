<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    /**
     * Store a newly created interest lead.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'facility_id' => 'required|integer|exists:facilities,id',
            'interested_date' => 'required|date|after_or_equal:today',
            'interested_time' => 'required|string',
        ]);

        Interest::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'facility_id' => $request->facility_id,
            'interested_date' => $request->interested_date,
            'interested_time' => $request->interested_time,
            'status' => 'NEW',
        ]);

        return redirect()->back()->with('success_interest', 'Thank you for your interest! Our team will contact you shortly. Please note: This does not reserve the slot.');
    }
}

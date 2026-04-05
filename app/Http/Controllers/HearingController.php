<?php

namespace App\Http\Controllers;

use App\Models\Hearing;

class HearingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hearings.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hearings.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hearing $hearing)
    {
        $hearing->load(['luponCase', 'attendances.luponMember']);

        return view('hearings.show', compact('hearing'));
    }
}

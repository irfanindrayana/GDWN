<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $member = Member::getAll();
        return view('member.index', compact('member'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        Member::create($validatedData);

        return redirect()->route('member.index')
            ->with('success', 'Member "' . $request->name . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $member = Member::find($id);
        if (!$member) {
            abort(404);
        }
        return view('member.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $member = Member::find($id);
        if (!$member) {
            abort(404);
        }
        return view('member.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        Member::updateData($id, $validatedData);

        return redirect()->route('member.index')
            ->with('success', 'Member "' . $request->name . '" berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $member = Member::find($id);
        if (!$member) {
            abort(404);
        }
        
        Member::deleteData($id);

        return redirect()->route('member.index')
            ->with('success', 'Member "' . $member->name . '" berhasil dihapus!');
    }
}

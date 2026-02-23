<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = PageSection::orderBy('section_key')->paginate(20);

        return view('admin.sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100', 'unique:page_sections,section_key'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta' => ['nullable', 'json'],
        ]);

        $data['meta'] = $data['meta'] ? json_decode($data['meta'], true) : null;

        PageSection::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Konten section berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PageSection $section)
    {
        return view('admin.sections.show', compact('section'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PageSection $section)
    {
        return view('admin.sections.edit', compact('section'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PageSection $section)
    {
        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100', Rule::unique('page_sections', 'section_key')->ignore($section->id)],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta' => ['nullable', 'json'],
        ]);

        $data['meta'] = $data['meta'] ? json_decode($data['meta'], true) : null;

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Konten section berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PageSection $section)
    {
        $section->delete();

        return redirect()->route('admin.sections.index')->with('success', 'Konten section berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kamars = Kamar::query();
            return DataTables::of($kamars)
                ->addIndexColumn()
                ->addColumn('action', function ($kamar) {
                    return '<div class="flex items-center gap-1">
                            <a href="' . route('kamar.edit', $kamar->id) . '"
                               class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button onclick="deleteKamar(' . $kamar->id . ')"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                    title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>';
                })
                ->editColumn('jenis', function ($kamar) {
                    if ($kamar->jenis === 'putra') {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Putra</span>';
                    }
                    return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400">Putri</span>';
                })
                ->rawColumns(['action', 'jenis'])
                ->make(true);
        }
        return view('pages.kamar.index', ['title' => 'Data Kamar']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.kamar.form', [
            'title' => 'Tambah Kamar',
            'kamar' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:50',
            'jenis' => 'required|in:putra,putri',
        ]);

        try {
            Kamar::create($validated);

            return redirect()->route('kamar.index')
                ->with('success', 'Kamar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kamar: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kamar $kamar)
    {
        return view('pages.kamar.form', [
            'title' => 'Edit Kamar',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kamar $kamar)
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:50',
            'jenis' => 'required|in:putra,putri',
        ]);

        try {
            $kamar->update($validated);

            return redirect()->route('kamar.index')
                ->with('success', 'Kamar berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kamar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kamar $kamar)
    {
        try {
            $kamar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kamar berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kamar: ' . $e->getMessage()
            ], 500);
        }
    }
}

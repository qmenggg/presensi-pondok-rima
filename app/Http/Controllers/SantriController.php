<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\User;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $santris = Santri::with(['user', 'kamar'])->select('santris.*');
            return DataTables::of($santris)
                ->addIndexColumn()
                ->addColumn('foto', function ($santri) {
                    $foto = $santri->foto ?? $santri->user->foto ?? null;
                    if ($foto) {
                        $fotoUrl = asset('storage/asset_santri/foto/' . $foto);
                        return '<img src="' . $fotoUrl . '" alt="Foto" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">';
                    }
                    $initial = strtoupper(substr($santri->user->nama ?? 'N', 0, 1));
                    return '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">' . $initial . '</div>';
                })
                ->addColumn('nama', function ($santri) {
                    return '<span class="font-medium text-gray-900 dark:text-white/90">' . ($santri->user->nama ?? '-') . '</span>';
                })
                ->addColumn('jenis_kelamin', function ($santri) {
                    $jk = $santri->user->jenis_kelamin ?? '';
                    if ($jk === 'L') {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                </svg>
                                Laki-laki
                            </span>';
                    } elseif ($jk === 'P') {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                </svg>
                                Perempuan
                            </span>';
                    }
                    return '<span class="text-gray-400 dark:text-gray-500 text-sm">-</span>';
                })
                ->addColumn('kamar', function ($santri) {
                    $kamar = $santri->kamar->nama_kamar ?? null;
                    if ($kamar) {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                ' . $kamar . '
                            </span>';
                    }
                    return '<span class="text-gray-400 dark:text-gray-500 text-sm italic">Belum ada kamar</span>';
                })
                ->addColumn('qr_code', function ($santri) {
                    if ($santri->qr_code) {
                        $qrFile = $santri->qr_code_file ?? '';
                        $nama = $santri->user->nama ?? 'Santri';
                        return '<button type="button" onclick="showQRModal(\'' . addslashes($santri->qr_code) . '\', \'' . addslashes($qrFile) . '\', \'' . addslashes($nama) . '\')"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 dark:bg-primary-900/30 dark:text-primary-400 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                <span class="font-mono">' . $santri->qr_code . '</span>
                            </button>';
                    }
                    return '<span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum digenerate</span>';
                })
                ->addColumn('action', function ($santri) {
                    $isAdmin = auth()->user()->role === 'admin';
                    if (!$isAdmin) {
                        return '<span class="text-gray-400 dark:text-gray-500 text-xs">-</span>';
                    }
                    return '<div class="flex items-center gap-1">
                            <a href="' . route('santri.edit', $santri->id) . '"
                               class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button onclick="deleteSantri(' . $santri->id . ')"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                    title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>';
                })
                ->rawColumns(['foto', 'nama', 'jenis_kelamin', 'kamar', 'qr_code', 'action'])
                ->make(true);
        }
        return view('pages.santri.index', ['title' => 'Data Santri']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kamars = Kamar::all();
        return view('pages.santri.form', [
            'title' => 'Tambah Santri',
            'santri' => null,
            'kamars' => $kamars,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Santri Store Request:', [
            'has_file_foto' => $request->hasFile('foto'),
            'all_data' => $request->all(),
            'files' => $_FILES ?? [],
        ]);

        $validated = $request->validate([
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_wali' => 'required|string|max:100',
            'kamar_id' => 'nullable|exists:kamars,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // SECURITY: Validate gender matches kamar type
        if (!empty($validated['kamar_id'])) {
            $kamar = Kamar::find($validated['kamar_id']);
            if ($kamar) {
                $genderMismatch = ($validated['jenis_kelamin'] === 'L' && $kamar->jenis !== 'putra') ||
                                  ($validated['jenis_kelamin'] === 'P' && $kamar->jenis !== 'putri');
                if ($genderMismatch) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Jenis kelamin santri tidak sesuai dengan jenis kamar.');
                }
            }
        }

        try {
            // Auto-generate username if empty
            $username = $validated['username'] ?? null;
            if (empty($username)) {
                $namaParts = explode(' ', trim($validated['nama']));
                $namaDepan = strtolower($namaParts[0] ?? 'santri');
                
                $kamarSlug = '';
                if (!empty($validated['kamar_id'])) {
                    $kamar = Kamar::find($validated['kamar_id']);
                    if ($kamar) {
                        $kamarSlug = '_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $kamar->nama_kamar));
                    }
                }
                
                $baseUsername = $namaDepan . $kamarSlug;
                $username = $baseUsername;
                $suffix = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . '_' . $suffix;
                    $suffix++;
                }
            }

            // Default password if empty
            $password = !empty($validated['password']) ? $validated['password'] : 'password123';

            // 1. Create User
            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'nama' => ucwords(strtolower($validated['nama'])),
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'role' => 'santri',
                'aktif' => true,
            ]);

            // 2. Handle foto upload
            $fotoName = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                // Create filename with user id to prevent overwrite
                $fotoName = Str::slug($validated['nama']) . '-' . $user->id . '.' . $foto->extension();
                
                // Resize and Save using Intervention Image
                $manager = new ImageManager(new Driver());
                $image = $manager->read($foto);
                $image->scale(width: 400); // Scale to width 400px
                
                $destinationPath = storage_path('app/public/asset_santri/foto');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $image->save($destinationPath . '/' . $fotoName);

                // Update user with foto filename
                $user->foto = $fotoName;
                $user->save();
            }

            // 3. Generate QR Code string
            $tahun = date('y');
            $gender = $validated['jenis_kelamin'] === 'L' ? 'L' : 'P';
            $userId = str_pad($user->id, 5, '0', STR_PAD_LEFT);
            $qrCode = 'QR-' . $tahun . $gender . $userId;

            // 4. Generate QR Code Image Name based on Name
            $namaSlug = Str::slug($validated['nama']);
            $qrFileName = $namaSlug . '.png';
            $qrPath = 'asset_santri/qrcode/' . $qrFileName;

            // Ensure directory exists
            $qrFolder = storage_path('app/public/asset_santri/qrcode');
            if (!file_exists($qrFolder)) {
                mkdir($qrFolder, 0777, true);
            }

            // Generate and Save QR Image
            $writer = new Writer(new GDLibRenderer(200));
            $writer->writeFile($qrCode, storage_path('app/public/' . $qrPath));

            // 5. Create Santri
            $santri = Santri::create([
                'user_id' => $user->id,
                'kamar_id' => $validated['kamar_id'] ?? null,
                'tempat_lahir' => ucwords(strtolower($validated['tempat_lahir'])),
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat' => $validated['alamat'],
                'nama_wali' => ucwords(strtolower($validated['nama_wali'])),
                'foto' => $fotoName,
                'qr_code' => $qrCode,
            ]);

            // 5. Generate QR Code Image
            $namaSantri = Str::slug($validated['nama']);
            $qrFolder = storage_path('app/public/asset_santri/qrcode');

            if (!file_exists($qrFolder)) {
                mkdir($qrFolder, 0777, true);
            }

            $qrFileName = $namaSantri . '-' . $user->id . '.png';
            $qrPath = 'asset_santri/qrcode/' . $qrFileName;

            $writer = new Writer(new GDLibRenderer(200));
            $writer->writeFile($qrCode, storage_path('app/public/' . $qrPath));

            // Save QR filename to database
            $santri->update(['qr_code_file' => $qrFileName]);

            return redirect()->route('santri.index')
                ->with('success', 'Santri berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Santri Create Error: ' . $e->getMessage());
            return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menambahkan santri: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Santri $santri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Santri $santri)
    {
        $santri->load(['user', 'kamar']);
        $kamars = Kamar::all();

        return view('pages.santri.form', [
            'title' => 'Edit Santri',
            'santri' => $santri,
            'kamars' => $kamars,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_wali' => 'required|string|max:100',
            'kamar_id' => 'nullable|exists:kamars,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // SECURITY: Validate gender matches kamar type
        if (!empty($validated['kamar_id'])) {
            $kamar = Kamar::find($validated['kamar_id']);
            if ($kamar) {
                $genderMismatch = ($validated['jenis_kelamin'] === 'L' && $kamar->jenis !== 'putra') ||
                                  ($validated['jenis_kelamin'] === 'P' && $kamar->jenis !== 'putri');
                if ($genderMismatch) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Jenis kelamin santri tidak sesuai dengan jenis kamar.');
                }
            }
        }

        try {
            // Check if name changed
            $oldName = $santri->user->nama;
            $newName = $validated['nama'];
            $nameChanged = $oldName !== $newName;

            // Prepare User Data (Username & Password input hidden, so we don't update them here)
            $userData = [
                'nama' => ucwords(strtolower($validated['nama'])),
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ];

            /* 
            // Password update hidden from this form
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            */

            // Handle Foto
            $fotoName = $santri->foto;

            // Scenario 1: New Photo Uploaded
            if ($request->hasFile('foto')) {
                // Delete old photo
                if ($santri->foto && Storage::disk('public')->exists('asset_santri/foto/' . $santri->foto)) {
                    Storage::disk('public')->delete('asset_santri/foto/' . $santri->foto);
                }

                $foto = $request->file('foto');
                // Create filename with user id to prevent overwrite
                $fotoName = Str::slug($validated['nama']) . '-' . $santri->user_id . '.' . $foto->extension();
                
                // Resize and Save using Intervention Image
                $manager = new ImageManager(new Driver());
                $image = $manager->read($foto);
                $image->scale(width: 400);
                
                $destinationPath = storage_path('app/public/asset_santri/foto');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $image->save($destinationPath . '/' . $fotoName);
            }
            // Scenario 2: No new photo, but Name Changed -> Rename existing photo
            elseif ($nameChanged && $santri->foto) {
                // Get extension from old filename
                $ext = pathinfo($santri->foto, PATHINFO_EXTENSION);
                $newFotoName = Str::slug($newName) . '.' . $ext;
                
                $oldPath = 'asset_santri/foto/' . $santri->foto;
                $newPath = 'asset_santri/foto/' . $newFotoName;

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                    $fotoName = $newFotoName;
                }
            }

            if ($fotoName) {
                $userData['foto'] = $fotoName;
            }

            // Handle QR Code Rename if Name Changed
            $qrFileName = $santri->qr_code_file;
            if ($nameChanged && $santri->qr_code_file) {
                 $newQrName = Str::slug($newName) . '.png';
                 $oldQrPath = 'asset_santri/qrcode/' . $santri->qr_code_file;
                 $newQrPath = 'asset_santri/qrcode/' . $newQrName;

                 if (Storage::disk('public')->exists($oldQrPath)) {
                     Storage::disk('public')->move($oldQrPath, $newQrPath);
                     $qrFileName = $newQrName;
                 }
            }

            // Update User
            $santri->user->update($userData);

            // Update Santri
            $santri->update([
                'kamar_id' => $validated['kamar_id'] ?? null,
                'tempat_lahir' => ucwords(strtolower($validated['tempat_lahir'])),
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat' => $validated['alamat'],
                'nama_wali' => ucwords(strtolower($validated['nama_wali'])),
                'foto' => $fotoName,
                'qr_code_file' => $qrFileName
            ]);

            return redirect()->route('santri.index')
                ->with('success', 'Santri berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate santri: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Santri $santri)
    {
        try {
            // Delete foto if exists
            if ($santri->foto) {
                Storage::disk('public')->delete('asset_santri/foto/' . $santri->foto);
            }
            if ($santri->user->foto && $santri->user->foto !== $santri->foto) {
                Storage::disk('public')->delete('asset_santri/foto/' . $santri->user->foto);
            }

            // Delete QR Code image
            if ($santri->qr_code_file) {
                Storage::disk('public')->delete('asset_santri/qrcode/' . $santri->qr_code_file);
            }

            // Delete related data first to prevent foreign key constraints
            // Using direct model queries to ensure deletion
            \App\Models\Absensi::where('santri_id', $santri->id)->delete();
            $santri->izins()->delete();
            $santri->subKegiatanSantris()->delete();

            // Delete Santri explicitly first
            $santri->delete();

            // Then delete User
            $santri->user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Santri berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus santri: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show QR Code
     */
    public function showQRCode($qrCode)
    {
        $santri = Santri::where('qr_code', $qrCode)->firstOrFail();

        if (!$santri->qr_code_file) {
            abort(404, 'QR Code tidak ditemukan');
        }

        $qrCodePath = 'asset_santri/qrcode/' . $santri->qr_code_file;

        if (!Storage::disk('public')->exists($qrCodePath)) {
            abort(404, 'File QR Code tidak ditemukan');
        }

        return response()->file(Storage::disk('public')->path($qrCodePath));
    }

    /**
     * Get QR Code Image by filename
     */
    public function getQRImage($filename)
    {
        $qrCodePath = 'asset_santri/qrcode/' . $filename;

        if (!Storage::disk('public')->exists($qrCodePath)) {
            abort(404, 'File QR Code tidak ditemukan');
        }

        $path = Storage::disk('public')->path($qrCodePath);
        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Display santri management page (activation & room transfer).
     */
    public function manajemen()
    {
        return view('pages.santri.manajemen', [
            'title' => 'Manajemen Santri',
        ]);
    }

    /**
     * Display QR Download page with kamar selection.
     */
    public function qrDownloadPage()
    {
        $this->requirePermission('santri.write');
        
        $kamarPutra = Kamar::where('jenis', 'putra')
            ->withCount(['santris' => function($q) {
                $q->whereNotNull('qr_code_file')
                  ->whereHas('user', fn($u) => $u->where('aktif', true));
            }])
            ->orderBy('nama_kamar')
            ->get();

        $kamarPutri = Kamar::where('jenis', 'putri')
            ->withCount(['santris' => function($q) {
                $q->whereNotNull('qr_code_file')
                  ->whereHas('user', fn($u) => $u->where('aktif', true));
            }])
            ->orderBy('nama_kamar')
            ->get();

        return view('pages.santri.qr-download', [
            'title' => 'Download QR Code Santri',
            'kamarPutra' => $kamarPutra,
            'kamarPutri' => $kamarPutri,
        ]);
    }

    /**
     * Download QR codes as ZIP file, organized by kamar.
     */
    public function downloadQRCodes(Request $request)
    {
        $this->requirePermission('santri.write');
        
        $validated = $request->validate([
            'kamar_ids' => 'required|array|min:1',
            'kamar_ids.*' => 'exists:kamars,id',
        ]);

        $kamarIds = $validated['kamar_ids'];

        // Get santris with QR codes from selected kamars
        $santris = Santri::with(['user', 'kamar'])
            ->whereIn('kamar_id', $kamarIds)
            ->whereNotNull('qr_code_file')
            ->whereHas('user', fn($q) => $q->where('aktif', true))
            ->get();

        if ($santris->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada QR code yang tersedia untuk kamar yang dipilih.');
        }

        // Create ZIP file
        $zipFileName = 'qr_codes_' . date('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
        }

        $addedCount = 0;
        foreach ($santris as $santri) {
            $qrFilePath = storage_path('app/public/asset_santri/qrcode/' . $santri->qr_code_file);
            
            if (file_exists($qrFilePath)) {
                // Create folder structure: KamarName/SantriName.png
                $kamarName = Str::slug($santri->kamar->nama_kamar ?? 'Tanpa-Kamar');
                $santriName = Str::slug($santri->user->nama ?? 'Unknown');
                $extension = pathinfo($santri->qr_code_file, PATHINFO_EXTENSION);
                
                $zipEntryName = $kamarName . '/' . $santriName . '.' . $extension;
                
                $zip->addFile($qrFilePath, $zipEntryName);
                $addedCount++;
            }
        }

        $zip->close();

        if ($addedCount === 0) {
            unlink($zipPath);
            return redirect()->back()->with('error', 'Tidak ada file QR code yang ditemukan.');
        }

        // Return download response and delete file after
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}

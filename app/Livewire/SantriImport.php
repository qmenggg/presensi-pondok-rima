<?php

namespace App\Livewire;

use App\Models\Kamar;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class SantriImport extends Component
{
    use WithFileUploads;

    public $file;
    public array $previewData = [];
    public array $parseErrors = []; // Renamed from $errors to avoid conflict
    public bool $showPreview = false;
    public bool $isProcessing = false;
    public int $successCount = 0;
    public int $failedCount = 0;
    public array $importResults = [];

    // Filter for form
    public string $filterJenisKelamin = '';
    public array $kamars = [];

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls|max:10240',
    ];

    public function mount(): void
    {
        $this->loadKamars();
    }

    public function updatedFilterJenisKelamin(): void
    {
        $this->loadKamars();
    }

    public function loadKamars(): void
    {
        $query = Kamar::orderBy('nama_kamar');
        
        if ($this->filterJenisKelamin === 'L') {
            $query->where('jenis', 'putra');
        } elseif ($this->filterJenisKelamin === 'P') {
            $query->where('jenis', 'putri');
        }
        
        $this->kamars = $query->get()->toArray();
    }

    public function updatedFile(): void
    {
        $this->validate();
        $this->parseFile();
    }

    public function parseFile(): void
    {
        $this->previewData = [];
        $this->parseErrors = [];
        $this->showPreview = false;

        try {
            $data = Excel::toArray(null, $this->file->getRealPath());
            
            if (empty($data) || empty($data[0])) {
                $this->parseErrors[] = 'File kosong atau format tidak valid.';
                return;
            }

            $rows = $data[0]; // First sheet
            $headers = array_map('strtolower', array_map('trim', $rows[0] ?? []));
            
            // Required columns
            $requiredColumns = ['nama_lengkap', 'jenis_kelamin', 'nama_kamar', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nama_wali'];
            $missingColumns = array_diff($requiredColumns, $headers);
            
            if (!empty($missingColumns)) {
                $this->parseErrors[] = 'Kolom berikut tidak ditemukan: ' . implode(', ', $missingColumns);
                return;
            }

            // Get column indices
            $indices = [];
            foreach ($requiredColumns as $col) {
                $indices[$col] = array_search($col, $headers);
            }

            // Parse data rows (skip header)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty(array_filter($row))) continue;

                $rowData = [
                    'row' => $i + 1,
                    'nama_lengkap' => ucwords(strtolower(trim($row[$indices['nama_lengkap']] ?? ''))),
                    'jenis_kelamin' => strtoupper(trim($row[$indices['jenis_kelamin']] ?? '')),
                    'nama_kamar' => trim($row[$indices['nama_kamar']] ?? ''),
                    'tempat_lahir' => ucwords(strtolower(trim($row[$indices['tempat_lahir']] ?? ''))),
                    'tanggal_lahir' => $this->parseDate($row[$indices['tanggal_lahir']] ?? ''),
                    'alamat' => trim($row[$indices['alamat']] ?? ''),
                    'nama_wali' => ucwords(strtolower(trim($row[$indices['nama_wali']] ?? ''))),
                    'rowErrors' => [],
                    'username' => '',
                ];

                // Validate row
                $rowData = $this->validateRow($rowData);

                // Generate username preview
                if (empty($rowData['rowErrors'])) {
                    $rowData['username'] = $this->generateUsername($rowData['nama_lengkap'], $rowData['nama_kamar']);
                }

                $this->previewData[] = $rowData;
            }

            $this->showPreview = true;

        } catch (\Exception $e) {
            $this->parseErrors[] = 'Error membaca file: ' . $e->getMessage();
        }
    }

    private function parseDate($value): string
    {
        if (empty($value)) return '';
        
        // If it's Excel numeric date
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('Y-m-d');
        }
        
        // Try parsing string date
        try {
            return date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function validateRow(array $row): array
    {
        // Required fields
        if (empty($row['nama_lengkap'])) {
            $row['rowErrors'][] = 'Nama lengkap wajib diisi';
        }

        if (!in_array($row['jenis_kelamin'], ['L', 'P'])) {
            $row['rowErrors'][] = 'Jenis kelamin harus L atau P';
        }

        if (empty($row['nama_kamar'])) {
            $row['rowErrors'][] = 'Nama kamar wajib diisi';
        } else {
            // Check if kamar exists
            $kamar = Kamar::where('nama_kamar', $row['nama_kamar'])->first();
            if (!$kamar) {
                $row['rowErrors'][] = 'Kamar tidak ditemukan';
            } else {
                // Validate gender match
                $expectedGender = $kamar->jenis === 'putra' ? 'L' : 'P';
                if ($row['jenis_kelamin'] !== $expectedGender) {
                    $row['rowErrors'][] = 'Jenis kelamin tidak sesuai dengan kamar';
                }
            }
        }

        if (empty($row['tempat_lahir'])) {
            $row['rowErrors'][] = 'Tempat lahir wajib diisi';
        }

        if (empty($row['tanggal_lahir'])) {
            $row['rowErrors'][] = 'Tanggal lahir wajib diisi';
        }

        if (empty($row['alamat'])) {
            $row['rowErrors'][] = 'Alamat wajib diisi';
        }

        if (empty($row['nama_wali'])) {
            $row['rowErrors'][] = 'Nama wali wajib diisi';
        }

        return $row;
    }

    private function generateUsername(string $namaLengkap, string $namaKamar): string
    {
        // Get first word of name
        $namaParts = explode(' ', trim($namaLengkap));
        $namaDepan = strtolower($namaParts[0] ?? 'santri');
        
        // Simplify kamar name
        $kamarSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $namaKamar));
        
        return $namaDepan . '_' . $kamarSlug;
    }

    public function processImport(): void
    {
        $this->isProcessing = true;
        $this->successCount = 0;
        $this->failedCount = 0;
        $this->importResults = [];

        foreach ($this->previewData as $index => $row) {
            if (!empty($row['rowErrors'])) {
                $this->failedCount++;
                $this->importResults[] = [
                    'row' => $row['row'],
                    'success' => false,
                    'message' => implode(', ', $row['rowErrors']),
                ];
                continue;
            }

            try {
                // Check duplicate username
                $username = $row['username'];
                $suffix = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $row['username'] . '_' . $suffix;
                    $suffix++;
                }

                // Create User
                $user = User::create([
                    'username' => $username,
                    'password' => Hash::make('password123'),
                    'nama' => $row['nama_lengkap'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'role' => 'santri',
                    'aktif' => true,
                ]);

                // Get kamar
                $kamar = Kamar::where('nama_kamar', $row['nama_kamar'])->first();

                // Generate QR Code
                $tahun = date('y');
                $gender = $row['jenis_kelamin'];
                $userId = str_pad($user->id, 5, '0', STR_PAD_LEFT);
                $qrCode = 'QR-' . $tahun . $gender . $userId;

                // Generate QR Code Image
                $qrFolder = storage_path('app/public/asset_santri/qrcode');
                if (!file_exists($qrFolder)) {
                    mkdir($qrFolder, 0777, true);
                }

                $namaSantri = Str::slug($row['nama_lengkap']);
                $qrFileName = $namaSantri . '-' . $user->id . '.png';
                $qrPath = 'asset_santri/qrcode/' . $qrFileName;

                $writer = new Writer(new GDLibRenderer(200));
                $writer->writeFile($qrCode, storage_path('app/public/' . $qrPath));

                // Create Santri
                Santri::create([
                    'user_id' => $user->id,
                    'kamar_id' => $kamar?->id,
                    'tempat_lahir' => $row['tempat_lahir'],
                    'tanggal_lahir' => $row['tanggal_lahir'],
                    'alamat' => $row['alamat'],
                    'nama_wali' => $row['nama_wali'],
                    'qr_code' => $qrCode,
                    'qr_code_file' => $qrFileName,
                ]);

                $this->successCount++;
                $this->importResults[] = [
                    'row' => $row['row'],
                    'success' => true,
                    'message' => "Berhasil: {$row['nama_lengkap']} (username: {$username})",
                ];

            } catch (\Exception $e) {
                $this->failedCount++;
                $this->importResults[] = [
                    'row' => $row['row'],
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ];
            }
        }

        $this->isProcessing = false;
        $this->showPreview = false;
        $this->previewData = [];
        $this->file = null;
    }

    public function resetForm(): void
    {
        $this->file = null;
        $this->previewData = [];
        $this->parseErrors = [];
        $this->showPreview = false;
        $this->importResults = [];
        $this->successCount = 0;
        $this->failedCount = 0;
    }

    public function render()
    {
        return view('livewire.santri-import');
    }
}

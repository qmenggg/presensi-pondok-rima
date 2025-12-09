<?php

namespace App\Http\Controllers;

use App\Exports\SantriExport;
use Maatwebsite\Excel\Facades\Excel;

class SantriExportController extends Controller
{
    /**
     * Export santri data to Excel
     */
    public function export()
    {
        $filename = 'data-santri-' . date('Y-m-d-His') . '.xlsx';
        return Excel::download(new SantriExport, $filename);
    }

    /**
     * Show template download page
     */
    public function templatePage()
    {
        return view('pages.santri.template', [
            'title' => 'Download Template',
        ]);
    }

    /**
     * Show import page
     */
    public function importPage()
    {
        return view('pages.santri.import', [
            'title' => 'Import Santri',
        ]);
    }
}

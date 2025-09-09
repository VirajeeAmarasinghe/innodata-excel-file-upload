<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function import(Request $request)
    {
        // Increase execution time limit
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:102400' // Max 10MB
        ]);

        try {
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));

            return redirect()->back()->with('success', 'Users imported successfully!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()->with('error', 'Import failed: ' . implode(' | ', array_slice($errors, 0, 5)));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}

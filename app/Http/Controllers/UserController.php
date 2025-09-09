<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

class UserController extends Controller
{
    public function import(Request $request)
    {
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '1024M');

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:102400'
        ]);

        $import = new UsersImport();
        Excel::import($import, $request->file('file'));

        if (!empty($import->errorsBag)) {
            return back()->with([
                'status' => 'Import completed with errors. Valid rows saved.',
                'import_errors' => $import->errorsBag
            ]);
        }

        return back()->with('status', 'Users imported successfully!');
    }

    public function index(Request $request)
    {
        $sort = $request->query('sort', 'created_at');
        $dir = $request->query('dir', 'desc');

        $allowed = ['name', 'email', 'contact_number', 'birthday', 'created_at'];
        if (!in_array($sort, $allowed))
            $sort = 'created_at';
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $users = User::orderBy($sort, $dir)->paginate(15)->withQueryString();
        return view('import.import', compact('users', 'sort', 'dir'));
    }
}

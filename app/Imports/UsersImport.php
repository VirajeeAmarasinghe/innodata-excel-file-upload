<?php

namespace App\Imports;

use App\Models\User;
use InvalidArgumentException;
use Maatwebsite\Excel\Concerns\ToModel;
use \Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;

class UsersImport implements ToModel, WithValidation, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    // public function model(array $row)
    // {

    //     return new User([
    //         'name' => $row[0],
    //         'email' => $row[1],
    //         'contact_number' => $row[2],
    //         'address' => $row[3],
    //         'birth_date' => Carbon::createFromFormat('Y-m-d', time: $row[4]),
    //     ]);


    // }

    public function model(array $row)
    {
        // Skip empty rows
        if (empty(array_filter($row))) {
            return null;
        }

        // Check if required fields are present
        if (empty($row['name']) || empty($row['email'])) {
            return null;
        }

        return new User([
            'name' => $row['name'] ?? '',
            'email' => $row['email'] ?? '',
            'contact_number' => $row['contact_number'] ?? '',
            'address' => $row['address'] ?? '',
            'birth_date' => $row['birthday'] ?? null
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        // Handle Excel date conversion only if birthday exists and is numeric
        if (isset($data['birthday']) && is_numeric($data['birthday'])) {
            try {
                $data['birthday'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['birthday'])->format('Y-m-d');
            } catch (\Exception $e) {
                // If conversion fails, keep original value
            }
        }

        return $data;
    }


    // WithHeadingRow automatically maps column headers to array keys
    // So your CSV should have headers: name, email, contact_number, address, birth_date

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'contact_number' => 'nullable|string',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
        ];
    }


}

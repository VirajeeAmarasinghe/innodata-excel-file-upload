<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithValidation, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public array $errorsBag = [];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row['name'] ?? '',
            'email' => $row['email'] ?? '',
            'contact_number' => $row['contact_number'],
            'address' => $row['address'] ?? null,
            'birth_date' => !empty($row['birthday']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birthday'])->format('Y-m-d') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'contact_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
        ];
    }
    public function customValidationMessages()
    {
        return [
            'contact_number.regex' => 'incorrect mobile number format',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $messages = implode(', ', $failure->errors());
            $this->errorsBag[] = "Error in row No. {$failure->row()} | {$messages}";
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
    public function batchSize(): int
    {
        return 1000;
    }

}

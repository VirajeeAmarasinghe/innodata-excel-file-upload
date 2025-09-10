<?php

namespace App\Imports;

use App\Models\User;
use Carbon\Carbon;
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
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
        $birthDate = null;

        if (!empty($row['birthday'])) {

            if (is_numeric($row['birthday'])) {
                try {
                    $birthDate = Date::excelToDateTimeObject($row['birthday'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $birthDate = null;
                    $this->errorsBag[] = "Error in row No. {$row['row']}: Invalid Excel date.";
                }
            } else {

                $formats = [
                    'm/d/Y h:i:s A',
                    'm/d/Y',
                    'Y-m-d',
                    'd-m-Y',
                    'd/m/Y',
                ];

                foreach ($formats as $format) {
                    try {
                        $birthDate = Carbon::createFromFormat($format, $row['birthday'])->format('Y-m-d');
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }


                if (!$birthDate) {
                    try {
                        $birthDate = Carbon::parse($row['birthday'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $birthDate = null;
                        $this->errorsBag[] = "Error in row No. {$row['row']}: Invalid date format.";
                    }
                }
            }
        }

        return new User([
            'name' => $row['name'] ?? '',
            'email' => $row['email'] ?? '',
            'contact_number' => $row['contact_number'] ?? '',
            'address' => $row['address'] ?? null,
            'birth_date' => $birthDate,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'contact_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'contact_number.regex' => 'Incorrect mobile number format',
        ];
    }

    /**
     * Collect validation failures
     */
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

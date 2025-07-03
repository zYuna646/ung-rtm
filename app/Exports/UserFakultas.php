<?php

namespace App\Exports;

use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class UserFakultas implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Get the role with slug 'fakultas'
        $role = Role::where('slug', 'fakultas')->first();
        
        // Fetch users with that role
        $users = User::where('role_id', $role->id)->get();
        
        // Transform the data into a collection of arrays
        return $users->map(function ($user) {
            return [
                'email' => $user->email,
                'fakultas' => $user->fakultas->name ?? 'N/A', // Ensure 'fakultas' relationship exists
                'password' => '12345678',
            ];
        });
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Email',
            'Fakultas',
            'Password',
        ];
    }

    /**
    * Apply styles to the sheet.
    *
    * @param Worksheet $sheet
    * @return void
    */
    public function styles(Worksheet $sheet)
    {
        // Set the first row (headings) to bold
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);

        // Add a border around all cells
        $sheet->getStyle('A1:C' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set alignment for all cells
        $sheet->getStyle('A1:C' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Set a background color for the header row
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00'); // Yellow background
    }
}

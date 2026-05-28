<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ExpenseReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Expense::query();

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('expense_date', [
                $this->request->start_date,
                $this->request->end_date
            ]);
        }

        return $query->orderBy('expense_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Deskripsi',
            'Catatan',
            'Jumlah'
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->expense_date->format('d/m/Y'),
            strtoupper($expense->type),
            $expense->description,
            $expense->notes ?: '-',
            $expense->amount,
        ];
    }
}

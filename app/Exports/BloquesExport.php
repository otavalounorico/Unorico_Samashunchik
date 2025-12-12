<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
use Maatwebsite\Excel\Concerns\WithStyles;     
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BloquesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $headings;

    // El constructor recibe los datos filtrados y los títulos desde el Controller
    public function __construct($data, $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    /**
    * Retorna la colección de datos para el Excel.
    */
    public function collection()
    {
        // Convertimos el array de datos en una Colección de Laravel
        return collect($this->data);
    }

    /**
    * Define los títulos de las columnas (Fila 1).
    */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
    * Opcional: Da estilo a la hoja (Poner la fila 1 en negrita).
    */
    public function styles(Worksheet $sheet)
    {
        return [
            // La fila 1 tendrá fuente en negrita
            1 => ['font' => ['bold' => true]],
        ];
    }
}
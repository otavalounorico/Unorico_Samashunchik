<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FallecidosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $headings;

    // Recibimos los datos y los encabezados desde el Controlador
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
        return $this->data;
    }

    /**
    * Define los títulos de las columnas (Fila 1).
    */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
    * Estilos: Pone en negrita la primera fila (encabezados).
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
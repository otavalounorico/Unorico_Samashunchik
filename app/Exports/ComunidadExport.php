<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComunidadExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $headings;

    /**
     * @param Collection $data Colección de arrays ya mapeados del controlador.
     * @param array $headings Arreglo con los encabezados de las columnas.
     */
    public function __construct(Collection $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function collection()
    {
        // Devuelve la colección de arrays mapeados que recibimos del controlador
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para la fila de encabezados (negrita y fondo oscuro)
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF1C2A48']]],
        ];
    }
}
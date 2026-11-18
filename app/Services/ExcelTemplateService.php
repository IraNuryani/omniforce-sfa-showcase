<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelTemplateService
{
    /**
     * Generate Excel by replacing placeholders in a template
     */
    public function generate(string $templatePath, array $data): Spreadsheet
    {
        if (file_exists($templatePath)) {
            $spreadsheet = IOFactory::load($templatePath);
        } else {
            // Fallback: create fresh spreadsheet if template file is missing
            $spreadsheet = new Spreadsheet();
        }

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $value = $cell->getValue();

                    if (is_string($value) && strpos($value, '{') !== false) {
                        $trimmedValue = trim($value);
                        if (str_starts_with($trimmedValue, '{') && str_ends_with($trimmedValue, '}')) {
                            $key = substr($trimmedValue, 1, -1);
                            $cell->setValue($data[$key] ?? $value);
                        } else {
                            $cell->setValue($this->replacePlaceholders($value, $data));
                        }
                    }
                }
            }
        }

        return $spreadsheet;
    }

    private function replacePlaceholders(string $text, array $data): string
    {
        return preg_replace_callback('/\{([\w\.]+)\}/', function ($matches) use ($data) {
            $key = $matches[1];
            return $data[$key] ?? $matches[0];
        }, $text);
    }
}

<?php

namespace App\Imports;

use App\Models\StartupTracking;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class StartupTrackingImporter
{
    /**
     * @var array<string, string>
     */
    private const HEADERS = [
        'type' => 'type',
        'program' => 'program',
        'project_title' => 'project_title',
        'name_of_proponent' => 'proponent_name',
        'contact_details' => 'contact_details',
        'amount' => 'amount',
        'class' => 'class',
        'status' => 'status',
        'promotional_assistance' => 'promotional_assistance',
        'revenue_growth_annual_cumulative' => 'revenue_growth',
        'jobs_created' => 'jobs_created',
        'investments_attracted_private_co_investment_seed_vc_grants' => 'investments_attracted',
        'market_reach_local_regional_global' => 'market_reach',
        'high_tech_exports_export_volume_value_if_applicable' => 'high_tech_exports',
        'social_impact' => 'social_impact',
        'next_possible_intervention' => 'next_possible_intervention',
    ];

    public function import(UploadedFile $file): int
    {
        try {
            $rows = strtolower($file->getClientOriginalExtension()) === 'xlsx'
                ? $this->xlsxRows($file->getRealPath())
                : $this->csvRows($file->getRealPath());

            return $this->persist($rows);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'file' => 'The spreadsheet could not be read. Please use a valid .xlsx or .csv file.',
            ]);
        }
    }

    /**
     * @param  list<array<int, string|null>>  $rows
     */
    private function persist(array $rows): int
    {
        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'The spreadsheet is empty.',
            ]);
        }

        $headerIndexes = $this->headerIndexes(array_shift($rows));
        $imported = 0;

        DB::transaction(function () use ($rows, $headerIndexes, &$imported): void {
            $previousType = null;

            foreach ($rows as $index => $row) {
                $values = $this->mapRow($row, $headerIndexes);

                if ($this->isEmptyRow($values)) {
                    continue;
                }

                if ($values['type'] === null) {
                    $values['type'] = $previousType;
                } else {
                    $previousType = $values['type'];
                }

                if ($values['type'] === null) {
                    $excelRow = $index + 2;

                    throw ValidationException::withMessages([
                        'file' => "TYPE is required on spreadsheet row {$excelRow} or a preceding data row.",
                    ]);
                }

                $values['class'] ??= 'STARTUP';

                StartupTracking::query()->updateOrCreate(
                    [
                        'type' => $values['type'],
                        'program' => $values['program'],
                        'project_title' => $values['project_title'],
                        'proponent_name' => $values['proponent_name'],
                    ],
                    $values,
                );

                $imported++;
            }
        });

        return $imported;
    }

    /**
     * @param  array<int, string|null>  $headerRow
     * @return array<string, int>
     */
    private function headerIndexes(array $headerRow): array
    {
        $availableHeaders = [];

        foreach ($headerRow as $index => $header) {
            $availableHeaders[$this->normalizeHeader((string) $header)] = $index;
        }

        $missing = array_diff(array_keys(self::HEADERS), array_keys($availableHeaders));

        if ($missing !== []) {
            $labels = collect($missing)
                ->map(fn (string $header): string => Str::of($header)->replace('_', ' ')->title())
                ->implode(', ');

            throw ValidationException::withMessages([
                'file' => "Missing required spreadsheet columns: {$labels}.",
            ]);
        }

        return collect(self::HEADERS)
            ->mapWithKeys(fn (string $field, string $header): array => [
                $field => $availableHeaders[$header],
            ])
            ->all();
    }

    private function normalizeHeader(string $header): string
    {
        return Str::of($header)
            ->replace("\u{FEFF}", '')
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, int>  $headerIndexes
     * @return array<string, string|null>
     */
    private function mapRow(array $row, array $headerIndexes): array
    {
        $values = [];

        foreach ($headerIndexes as $field => $index) {
            $value = trim((string) ($row[$index] ?? ''));
            $values[$field] = $value === '' || $value === '-' ? null : $value;
        }

        $values['amount'] = $this->normalizeAmount($values['amount']);

        return $values;
    }

    /**
     * @param  array<string, string|null>  $values
     */
    private function isEmptyRow(array $values): bool
    {
        return collect($values)->every(fn (?string $value): bool => $value === null);
    }

    private function normalizeAmount(?string $amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        $isNegative = str_contains($amount, '-') || (
            str_contains($amount, '(') && str_contains($amount, ')')
        );
        $normalized = preg_replace('/[^0-9.,]/', '', $amount);

        if ($normalized === '') {
            return null;
        }

        $lastDot = strrpos($normalized, '.');
        $lastComma = strrpos($normalized, ',');
        $decimalPosition = max($lastDot === false ? -1 : $lastDot, $lastComma === false ? -1 : $lastComma);
        $decimalDigits = $decimalPosition >= 0 ? substr($normalized, $decimalPosition + 1) : '';
        $hasDecimalPart = in_array(strlen($decimalDigits), [1, 2], true);
        $integerPart = $hasDecimalPart ? substr($normalized, 0, $decimalPosition) : $normalized;

        if (strlen($decimalDigits) > 3) {
            $integerDigits = preg_replace('/\D/', '', substr($normalized, 0, $decimalPosition));
            $floatingAmount = (float) ($integerDigits.'.'.$decimalDigits);

            return ($isNegative ? '-' : '').number_format($floatingAmount, 2, '.', '');
        }

        $integerDigits = preg_replace('/\D/', '', $integerPart);

        if ($integerDigits === '') {
            return null;
        }

        return ($isNegative ? '-' : '').$integerDigits.($hasDecimalPart ? '.'.$decimalDigits : '');
    }

    /**
     * @return list<array<int, string|null>>
     */
    private function csvRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Unable to open CSV file.');
        }

        $rows = [];

        try {
            while (($row = fgetcsv($handle, escape: '')) !== false) {
                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }

    /**
     * @return list<array<int, string|null>>
     */
    private function xlsxRows(string $path): array
    {
        $archive = new ZipArchive;

        if ($archive->open($path) !== true) {
            throw new RuntimeException('Unable to open XLSX archive.');
        }

        try {
            $sharedStrings = $this->sharedStrings($archive);
            $worksheet = $this->xml($this->archiveContents($archive, $this->firstWorksheetPath($archive)));

            return $this->worksheetRows($worksheet, $sharedStrings);
        } finally {
            $archive->close();
        }
    }

    /**
     * @return list<string>
     */
    private function sharedStrings(ZipArchive $archive): array
    {
        $contents = $archive->getFromName('xl/sharedStrings.xml');

        if ($contents === false) {
            return [];
        }

        $strings = [];

        foreach ($this->xml($contents)->xpath('//*[local-name()="si"]') ?: [] as $item) {
            $strings[] = dom_import_simplexml($item)->textContent;
        }

        return $strings;
    }

    private function firstWorksheetPath(ZipArchive $archive): string
    {
        $workbook = $this->xml($this->archiveContents($archive, 'xl/workbook.xml'));
        $sheet = ($workbook->xpath('//*[local-name()="sheet"]') ?: [])[0] ?? null;

        if (! $sheet instanceof SimpleXMLElement) {
            throw new RuntimeException('The workbook has no worksheets.');
        }

        $namespaces = $workbook->getNamespaces(true);
        $relationshipId = isset($namespaces['r'])
            ? (string) $sheet->attributes($namespaces['r'])['id']
            : '';
        $relationships = $this->xml($this->archiveContents($archive, 'xl/_rels/workbook.xml.rels'));

        foreach ($relationships->xpath('//*[local-name()="Relationship"]') ?: [] as $relationship) {
            if ((string) $relationship['Id'] === $relationshipId) {
                $target = ltrim((string) $relationship['Target'], '/');

                return str_starts_with($target, 'xl/') ? $target : 'xl/'.$target;
            }
        }

        throw new RuntimeException('Unable to locate the first worksheet.');
    }

    /**
     * @param  list<string>  $sharedStrings
     * @return list<array<int, string|null>>
     */
    private function worksheetRows(SimpleXMLElement $worksheet, array $sharedStrings): array
    {
        $rows = [];

        foreach ($worksheet->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [] as $row) {
            $values = [];

            foreach ($row->xpath('./*[local-name()="c"]') ?: [] as $cell) {
                $reference = (string) $cell['r'];
                preg_match('/^[A-Z]+/i', $reference, $matches);
                $index = $this->columnIndex($matches[0] ?? 'A');
                $values[$index] = $this->cellValue($cell, $sharedStrings);
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = $values;
            }
        }

        return $rows;
    }

    /**
     * @param  list<string>  $sharedStrings
     */
    private function cellValue(SimpleXMLElement $cell, array $sharedStrings): ?string
    {
        $type = (string) $cell['t'];

        if ($type === 'inlineStr') {
            return trim(dom_import_simplexml($cell)->textContent);
        }

        $valueNode = ($cell->xpath('./*[local-name()="v"]') ?: [])[0] ?? null;

        if (! $valueNode instanceof SimpleXMLElement) {
            return null;
        }

        $value = (string) $valueNode;

        return $type === 's' ? ($sharedStrings[(int) $value] ?? null) : $value;
    }

    private function columnIndex(string $letters): int
    {
        $index = 0;

        foreach (str_split(strtoupper($letters)) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function archiveContents(ZipArchive $archive, string $path): string
    {
        $contents = $archive->getFromName($path);

        if ($contents === false) {
            throw new RuntimeException("Missing XLSX entry: {$path}");
        }

        return $contents;
    }

    private function xml(string $contents): SimpleXMLElement
    {
        $xml = simplexml_load_string($contents);

        if (! $xml instanceof SimpleXMLElement) {
            throw new RuntimeException('Invalid XML in spreadsheet.');
        }

        return $xml;
    }
}

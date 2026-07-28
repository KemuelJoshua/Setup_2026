<?php

namespace App\Imports;

use App\Models\ProjectImpact;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class ProjectImpactImporter
{
    /**
     * @var array<string, string>
     */
    private const CSV_HEADERS = [
        'source_sheet' => 'source_sheet',
        'record_number' => 'record_number',
        'project_title' => 'project_title',
        'proponent' => 'proponent',
        'classification' => 'classification',
        'sub_classification' => 'sub_classification',
        'ip_type' => 'ip_type',
        'field_of_technology' => 'field_of_technology',
        'program_intervention' => 'program_intervention',
        'amount_assistance' => 'amount_assistance',
        'date_assistance' => 'date_assistance',
        'project_status' => 'project_status',
        'date_completed' => 'date_completed',
        'readiness_before' => 'readiness_before',
        'readiness_after' => 'readiness_after',
        'other_interventions' => 'other_interventions',
        'revenue_amount' => 'revenue_amount',
        'technology_commercialized' => 'technology_commercialized',
        'jobs_created' => 'jobs_created',
        'investment_leveraged' => 'investment_leveraged',
        'efficiency_improved' => 'efficiency_improved',
        'communities_served' => 'communities_served',
        'priority_sectors_benefited' => 'priority_sectors_benefited',
        'ip_assets_utilized' => 'ip_assets_utilized',
        'spin_offs_formed' => 'spin_offs_formed',
        'human_capital_developed' => 'human_capital_developed',
        'other_impacts' => 'other_impacts',
        'impact_narrative' => 'impact_narrative',
    ];

    public function import(UploadedFile $file): int
    {
        try {
            $records = strtolower($file->getClientOriginalExtension()) === 'xlsx'
                ? $this->workbookRecords($file->getRealPath())
                : $this->csvRecords($file->getRealPath());

            return $this->persist($records);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'file' => 'The workbook could not be read. Please upload a valid Project Impact .xlsx or normalized .csv file.',
            ]);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $records
     */
    private function persist(array $records): int
    {
        if ($records === []) {
            throw ValidationException::withMessages([
                'file' => 'No project impact records were found in the uploaded file.',
            ]);
        }

        return DB::transaction(function () use ($records): int {
            foreach ($records as $record) {
                ProjectImpact::query()->updateOrCreate(
                    [
                        'source_sheet' => $record['source_sheet'],
                        'record_number' => $record['record_number'],
                        'project_title' => $record['project_title'],
                    ],
                    $record,
                );
            }

            return count($records);
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function workbookRecords(string $path): array
    {
        $records = [];

        foreach ($this->xlsxRowsBySheet($path) as $sheetName => $rows) {
            foreach ($rows as $row) {
                $record = match (true) {
                    str_starts_with($sheetName, 'Pre-Comm & Comm') => $this->preCommercialRecord($sheetName, $row),
                    str_starts_with($sheetName, 'HIRANG') => $this->hirangRecord($sheetName, $row),
                    str_starts_with($sheetName, 'TECH TRANS') => $this->technologyTransferRecord($sheetName, $row),
                    str_starts_with($sheetName, 'IPRAP') => $this->iprapRecord($sheetName, $row),
                    str_starts_with($sheetName, 'eIPRAP') => $this->eiprapRecord($sheetName, $row),
                    default => null,
                };

                if ($record !== null) {
                    $records[] = $record;
                }
            }
        }

        return $records;
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function preCommercialRecord(string $sheetName, array $row): ?array
    {
        if (! $this->isDataRow($row, 0, 1)) {
            return null;
        }

        $interventions = [
            $this->intervention($row, 6, 7, 8, 9, 10, 11, 12),
            $this->intervention($row, 13, 14, 15, null, null, 16, 17),
            $this->intervention($row, 18, 19, 20, 21, null, 22, 23),
            $this->intervention($row, 24, 25, 26, 27, null, 28, 29),
        ];
        $interventions = array_values(array_filter(
            $interventions,
            fn (array $intervention): bool => collect($intervention)->filter()->isNotEmpty(),
        ));
        $primary = $interventions[0] ?? [];

        return $this->baseRecord($sheetName, $row, 0, 1, 2, 3, 5, [
            'program_intervention' => $primary['program'] ?? null,
            'amount_assistance' => $primary['amount'] ?? null,
            'date_assistance' => $primary['date'] ?? null,
            'project_status' => $primary['status'] ?? null,
            'date_completed' => $primary['date_completed'] ?? null,
            'readiness_before' => $primary['readiness_before'] ?? null,
            'readiness_after' => $primary['readiness_after'] ?? null,
            ...$this->impactFields($row, 30),
            'impact_narrative' => $this->value($row, 41),
            'additional_data' => $this->cleanArray([
                'secondary_classification' => $this->value($row, 4),
                'interventions' => $interventions,
            ]),
        ]);
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function hirangRecord(string $sheetName, array $row): ?array
    {
        if (! $this->isDataRow($row, 0, 3)) {
            return null;
        }

        $cohort = $this->value($row, 1);
        $graduates = $this->value($row, 3);
        $group = Str::of((string) $graduates)->before("\n")->limit(100)->toString();

        return $this->baseRecord($sheetName, $row, 0, null, 3, null, null, [
            'project_title' => trim("HIRANG Cohort {$cohort}: {$group}", ': '),
            'program_intervention' => $this->value($row, 2),
            'other_interventions' => $this->value($row, 5),
            ...$this->impactFields($row, 6),
            'spin_offs_formed' => $this->firstValue(
                $this->value($row, 14),
                $this->value($row, 4),
            ),
            'impact_narrative' => $this->value($row, 17),
            'additional_data' => $this->cleanArray([
                'cohort' => $cohort,
                'graduates' => $graduates,
                'spin_off_startup_established' => $this->value($row, 4),
            ]),
        ]);
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function technologyTransferRecord(string $sheetName, array $row): ?array
    {
        if (! $this->isDataRow($row, 0, 1)) {
            return null;
        }

        return $this->baseRecord($sheetName, $row, 0, 1, 2, 3, 4, [
            'readiness_after' => $this->value($row, 6),
            'other_interventions' => $this->value($row, 7),
            ...$this->impactFields($row, 9),
            'impact_narrative' => $this->firstValue(
                $this->value($row, 20),
                $this->value($row, 21),
            ),
            'additional_data' => $this->cleanArray([
                'date_irl_assessment' => $this->normalizeDate($this->value($row, 5)),
                'intervention_date' => $this->normalizeDate($this->value($row, 8)),
            ]),
        ]);
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function iprapRecord(string $sheetName, array $row): ?array
    {
        if (! $this->isDataRow($row, 0, 2)) {
            return null;
        }

        return $this->baseRecord($sheetName, $row, 0, 2, 3, 5, null, [
            'ip_type' => $this->value($row, 1),
            'field_of_technology' => $this->value($row, 6),
            'other_interventions' => $this->value($row, 16),
            'project_status' => $this->value($row, 17),
            ...$this->impactFields($row, 18),
            'impact_narrative' => $this->value($row, 29),
            'additional_data' => $this->cleanArray([
                'inventor_researcher_count' => $this->value($row, 4),
                'date_filing' => $this->normalizeDate($this->value($row, 7)),
                'date_granted' => $this->normalizeDate($this->value($row, 8)),
                'claims' => $this->value($row, 9),
                'length_of_prosecution' => $this->value($row, 10),
                'office_actions' => $this->value($row, 11),
                'forward_citation' => $this->value($row, 12),
                'backward_citation' => $this->value($row, 13),
                'pct_international_filing' => $this->value($row, 14),
                'npe_filing' => $this->value($row, 15),
            ]),
        ]);
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, mixed>|null
     */
    private function eiprapRecord(string $sheetName, array $row): ?array
    {
        if (! $this->isDataRow($row, 0, 2)) {
            return null;
        }

        return $this->baseRecord($sheetName, $row, 0, 2, 3, 5, null, [
            'ip_type' => $this->value($row, 1),
            'field_of_technology' => $this->value($row, 6),
            'other_interventions' => $this->value($row, 14),
            'project_status' => $this->value($row, 15),
            ...$this->impactFields($row, 16),
            'impact_narrative' => $this->value($row, 27),
            'additional_data' => $this->cleanArray([
                'inventor_researcher_count' => $this->value($row, 4),
                'date_filing_npe' => $this->normalizeDate($this->value($row, 7)),
                'date_granted_npe' => $this->normalizeDate($this->value($row, 8)),
                'claims' => $this->value($row, 9),
                'length_of_prosecution' => $this->value($row, 10),
                'office_actions' => $this->value($row, 11),
                'forward_citation' => $this->value($row, 12),
                'backward_citation' => $this->value($row, 13),
            ]),
        ]);
    }

    /**
     * @param  array<int, string|null>  $row
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function baseRecord(
        string $sheetName,
        array $row,
        int $recordNumberIndex,
        ?int $titleIndex,
        int $proponentIndex,
        ?int $classificationIndex,
        ?int $subClassificationIndex,
        array $overrides,
    ): array {
        return [
            'source_sheet' => $sheetName,
            'record_number' => $this->normalizeRecordNumber($this->value($row, $recordNumberIndex)),
            'project_title' => $titleIndex === null ? '' : (string) $this->value($row, $titleIndex),
            'proponent' => $this->value($row, $proponentIndex),
            'classification' => $classificationIndex === null ? null : $this->value($row, $classificationIndex),
            'sub_classification' => $subClassificationIndex === null ? null : $this->value($row, $subClassificationIndex),
            'ip_type' => null,
            'field_of_technology' => null,
            'program_intervention' => null,
            'amount_assistance' => null,
            'date_assistance' => null,
            'project_status' => null,
            'date_completed' => null,
            'readiness_before' => null,
            'readiness_after' => null,
            'other_interventions' => null,
            'revenue_amount' => null,
            'technology_commercialized' => null,
            'jobs_created' => null,
            'investment_leveraged' => null,
            'efficiency_improved' => null,
            'communities_served' => null,
            'priority_sectors_benefited' => null,
            'ip_assets_utilized' => null,
            'spin_offs_formed' => null,
            'human_capital_developed' => null,
            'other_impacts' => null,
            'impact_narrative' => null,
            'additional_data' => null,
            ...$overrides,
        ];
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, string|null>
     */
    private function impactFields(array $row, int $startIndex): array
    {
        return [
            'revenue_amount' => $this->value($row, $startIndex),
            'technology_commercialized' => $this->value($row, $startIndex + 1),
            'jobs_created' => $this->value($row, $startIndex + 2),
            'investment_leveraged' => $this->value($row, $startIndex + 3),
            'efficiency_improved' => $this->value($row, $startIndex + 4),
            'communities_served' => $this->value($row, $startIndex + 5),
            'priority_sectors_benefited' => $this->value($row, $startIndex + 6),
            'ip_assets_utilized' => $this->value($row, $startIndex + 7),
            'spin_offs_formed' => $this->value($row, $startIndex + 8),
            'human_capital_developed' => $this->value($row, $startIndex + 9),
            'other_impacts' => $this->value($row, $startIndex + 10),
        ];
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<string, string|null>
     */
    private function intervention(
        array $row,
        int $program,
        int $amount,
        int $date,
        ?int $status,
        ?int $dateCompleted,
        int $readinessBefore,
        int $readinessAfter,
    ): array {
        return [
            'program' => $this->value($row, $program),
            'amount' => $this->normalizeAmount($this->value($row, $amount)),
            'date' => $this->normalizeDate($this->value($row, $date)),
            'status' => $status === null ? null : $this->value($row, $status),
            'date_completed' => $dateCompleted === null ? null : $this->normalizeDate($this->value($row, $dateCompleted)),
            'readiness_before' => $this->value($row, $readinessBefore),
            'readiness_after' => $this->value($row, $readinessAfter),
        ];
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isDataRow(array $row, int $numberIndex, int $titleIndex): bool
    {
        $number = $this->value($row, $numberIndex);
        $title = $this->value($row, $titleIndex);

        return $number !== null
            && is_numeric($number)
            && $title !== null
            && ! str_contains(Str::lower($title), 'project title')
            && ! str_contains(Str::lower($title), 'technology title');
    }

    private function normalizeRecordNumber(?string $number): ?string
    {
        if ($number === null) {
            return null;
        }

        return is_numeric($number) ? (string) (int) $number : $number;
    }

    private function normalizeDate(?string $date): ?string
    {
        if ($date === null || in_array(Str::lower($date), ['n/a', 'na', 'none'], true)) {
            return null;
        }

        if (is_numeric($date)) {
            return CarbonImmutable::create(1899, 12, 30)
                ->addDays((int) floor((float) $date))
                ->format('Y-m-d');
        }

        foreach (['m/d/Y', 'm/d/y', 'Y-m-d'] as $format) {
            try {
                $parsed = CarbonImmutable::createFromFormat($format, $date);

                if ($parsed !== null) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function normalizeAmount(?string $amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        $normalized = preg_replace('/[^0-9.,]/', '', $amount);

        if ($normalized === '') {
            return null;
        }

        $lastDot = strrpos($normalized, '.');
        $lastComma = strrpos($normalized, ',');
        $decimalPosition = max($lastDot === false ? -1 : $lastDot, $lastComma === false ? -1 : $lastComma);
        $decimalDigits = $decimalPosition >= 0 ? substr($normalized, $decimalPosition + 1) : '';
        $hasDecimalPart = in_array(strlen($decimalDigits), [1, 2], true);

        if (strlen($decimalDigits) > 3) {
            $integerDigits = preg_replace('/\D/', '', substr($normalized, 0, $decimalPosition));

            return number_format((float) ($integerDigits.'.'.$decimalDigits), 2, '.', '');
        }

        $integerPart = $hasDecimalPart ? substr($normalized, 0, $decimalPosition) : $normalized;
        $integerDigits = preg_replace('/\D/', '', $integerPart);

        return $integerDigits === ''
            ? null
            : $integerDigits.($hasDecimalPart ? '.'.$decimalDigits : '');
    }

    private function firstValue(?string ...$values): ?string
    {
        foreach ($values as $value) {
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>|null
     */
    private function cleanArray(array $values): ?array
    {
        $filtered = array_filter($values, fn (mixed $value): bool => $value !== null && $value !== []);

        return $filtered === [] ? null : $filtered;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function value(array $row, int $index): ?string
    {
        $value = trim((string) ($row[$index] ?? ''));

        return $value === '' ? null : $value;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function csvRecords(string $path): array
    {
        $rows = $this->csvRows($path);

        if ($rows === []) {
            return [];
        }

        $headers = [];

        foreach (array_shift($rows) as $index => $header) {
            $headers[$this->normalizeHeader((string) $header)] = $index;
        }

        foreach (['source_sheet', 'project_title'] as $requiredHeader) {
            if (! array_key_exists($requiredHeader, $headers)) {
                throw ValidationException::withMessages([
                    'file' => 'The normalized CSV must contain Source Sheet and Project Title columns.',
                ]);
            }
        }

        $records = [];

        foreach ($rows as $row) {
            $record = [];

            foreach (self::CSV_HEADERS as $header => $field) {
                $record[$field] = $this->value($row, $headers[$header] ?? -1);
            }

            if (($record['source_sheet'] ?? null) === null && ($record['project_title'] ?? null) === null) {
                continue;
            }

            $record['amount_assistance'] = $this->normalizeAmount($record['amount_assistance'] ?? null);
            $record['date_assistance'] = $this->normalizeDate($record['date_assistance'] ?? null);
            $record['date_completed'] = $this->normalizeDate($record['date_completed'] ?? null);
            $record['additional_data'] = null;
            $records[] = $record;
        }

        return $records;
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
     * @return array<string, list<array<int, string|null>>>
     */
    private function xlsxRowsBySheet(string $path): array
    {
        $archive = new ZipArchive;

        if ($archive->open($path) !== true) {
            throw new RuntimeException('Unable to open XLSX archive.');
        }

        try {
            $sharedStrings = $this->sharedStrings($archive);
            $workbook = $this->xml($this->archiveContents($archive, 'xl/workbook.xml'));
            $relationships = $this->workbookRelationships($archive);
            $namespaces = $workbook->getNamespaces(true);
            $sheets = [];

            foreach ($workbook->xpath('//*[local-name()="sheet"]') ?: [] as $sheet) {
                $relationshipId = (string) $sheet->attributes($namespaces['r'])['id'];
                $target = ltrim($relationships[$relationshipId] ?? '', '/');

                if ($target === '') {
                    continue;
                }

                $worksheetPath = str_starts_with($target, 'xl/') ? $target : 'xl/'.$target;
                $worksheet = $this->xml($this->archiveContents($archive, $worksheetPath));
                $sheets[(string) $sheet['name']] = $this->worksheetRows($worksheet, $sharedStrings);
            }

            return $sheets;
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

    /**
     * @return array<string, string>
     */
    private function workbookRelationships(ZipArchive $archive): array
    {
        $relationships = [];
        $xml = $this->xml($this->archiveContents($archive, 'xl/_rels/workbook.xml.rels'));

        foreach ($xml->xpath('//*[local-name()="Relationship"]') ?: [] as $relationship) {
            $relationships[(string) $relationship['Id']] = (string) $relationship['Target'];
        }

        return $relationships;
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
                preg_match('/^[A-Z]+/i', (string) $cell['r'], $matches);
                $values[$this->columnIndex($matches[0] ?? 'A')] = $this->cellValue($cell, $sharedStrings);
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
            throw new RuntimeException('Invalid XML in workbook.');
        }

        return $xml;
    }
}

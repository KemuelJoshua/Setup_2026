<?php

use App\Models\ProjectImpact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function projectImpactPayload(array $overrides = []): array
{
    return array_merge([
        'source_sheet' => 'TECH TRANS 2021-2025',
        'record_number' => '1',
        'project_title' => 'BUHAWI',
        'proponent' => 'DOST-MIRDC',
        'classification' => 'RDI',
        'sub_classification' => null,
        'ip_type' => null,
        'field_of_technology' => 'Advanced Manufacturing',
        'program_intervention' => 'IRL Assessment',
        'amount_assistance' => '100000.00',
        'date_assistance' => '2025-03-04',
        'project_status' => 'Completed',
        'date_completed' => '2025-05-01',
        'readiness_before' => 'IRL 1',
        'readiness_after' => 'IRL 3',
        'other_interventions' => 'Technical mentoring',
        'revenue_amount' => 'PHP 500,000',
        'technology_commercialized' => 'Local licensing',
        'jobs_created' => '10',
        'investment_leveraged' => 'PHP 1,000,000',
        'efficiency_improved' => '20% productivity increase',
        'communities_served' => 'Metro Manila',
        'priority_sectors_benefited' => 'Women-led MSMEs',
        'ip_assets_utilized' => 'Utility model licensed',
        'spin_offs_formed' => 'BUHAWI Technologies',
        'human_capital_developed' => '15 engineers trained',
        'other_impacts' => 'Regional recognition',
        'impact_narrative' => 'The technology reached initial commercialization.',
    ], $overrides);
}

function projectImpactCsv(array $rows): string
{
    $stream = fopen('php://temp', 'w+');

    foreach ($rows as $row) {
        fputcsv($stream, $row, escape: '');
    }

    rewind($stream);
    $contents = stream_get_contents($stream);
    fclose($stream);

    return $contents;
}

function projectImpactXlsx(string $sheetName, array $rows): string
{
    $temporaryPath = tempnam(sys_get_temp_dir(), 'project-impact-');
    $archive = new ZipArchive;
    $archive->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $escapedSheetName = htmlspecialchars($sheetName, ENT_XML1);
    $archive->addFromString(
        'xl/workbook.xml',
        '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="'.$escapedSheetName.'" sheetId="1" r:id="rId1"/></sheets></workbook>',
    );
    $archive->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML);

    $columnName = function (int $index): string {
        $name = '';

        while ($index >= 0) {
            $name = chr(($index % 26) + 65).$name;
            $index = intdiv($index, 26) - 1;
        }

        return $name;
    };
    $xmlRows = collect($rows)->map(function (array $row, int $rowIndex) use ($columnName): string {
        $cells = collect($row)->map(function ($value, int $columnIndex) use ($rowIndex, $columnName): string {
            $reference = $columnName($columnIndex).($rowIndex + 1);
            $escaped = htmlspecialchars((string) $value, ENT_XML1);

            return "<c r=\"{$reference}\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
        })->implode('');

        return '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
    })->implode('');
    $archive->addFromString(
        'xl/worksheets/sheet1.xml',
        '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$xmlRows.'</sheetData></worksheet>',
    );
    $archive->close();

    $contents = file_get_contents($temporaryPath);
    unlink($temporaryPath);

    return $contents;
}

test('guests cannot access project impact tracking', function () {
    $this->get(route('admin.project-impact-tracking.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view and filter project impact records', function () {
    $user = User::factory()->create();
    ProjectImpact::factory()->create([
        'source_sheet' => 'TECH TRANS 2021-2025',
        'project_title' => 'BUHAWI',
        'classification' => 'RDI',
    ]);
    ProjectImpact::factory()->create([
        'source_sheet' => 'IPRAP 2021-2025',
        'project_title' => 'Rice Retort Technology',
        'classification' => 'MSME',
    ]);

    $this->actingAs($user)
        ->get(route('admin.project-impact-tracking.index', [
            'search' => 'BUHAWI',
            'source_sheet' => 'TECH TRANS 2021-2025',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/project-impact-tracking/Index')
            ->has('records.data', 1)
            ->where('records.data.0.project_title', 'BUHAWI')
            ->has('options.sourceSheets'));
});

test('authenticated users can create update and delete project impact records', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.project-impact-tracking.store'), projectImpactPayload())
        ->assertRedirect(route('admin.project-impact-tracking.index'))
        ->assertSessionHas('success');

    $record = ProjectImpact::query()->firstOrFail();

    expect($record->project_title)->toBe('BUHAWI')
        ->and($record->amount_assistance)->toBe('100000.00');

    $this->actingAs($user)
        ->put(
            route('admin.project-impact-tracking.update', $record),
            projectImpactPayload(['project_status' => 'Ongoing']),
        )
        ->assertRedirect(route('admin.project-impact-tracking.index'));

    expect($record->refresh()->project_status)->toBe('Ongoing');

    $this->actingAs($user)
        ->delete(route('admin.project-impact-tracking.destroy', $record))
        ->assertRedirect(route('admin.project-impact-tracking.index'));

    $this->assertModelMissing($record);
});

test('project impact forms validate required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.project-impact-tracking.store'), [
            'source_sheet' => '',
            'project_title' => '',
            'amount_assistance' => -1,
        ])
        ->assertSessionHasErrors([
            'source_sheet',
            'project_title',
            'amount_assistance',
        ]);
});

test('authenticated users can import normalized project impact csv files', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->createWithContent(
        'project-impact.csv',
        projectImpactCsv([
            [
                'Source Sheet',
                'Record Number',
                'Project Title',
                'Proponent',
                'Classification',
                'Amount Assistance',
                'Project Status',
                'Jobs Created',
                'Impact Narrative',
            ],
            [
                'TECH TRANS 2021-2025',
                '1',
                'BUHAWI',
                'DOST-MIRDC',
                'RDI',
                '100,000.00',
                'Completed',
                '10',
                'Commercialization initiated.',
            ],
        ]),
    );

    $this->actingAs($user)
        ->post(route('admin.project-impact-tracking.import'), ['file' => $file])
        ->assertRedirect(route('admin.project-impact-tracking.index'))
        ->assertSessionHas('success', '1 project impact records imported successfully.');

    $record = ProjectImpact::query()->firstOrFail();

    expect($record->project_title)->toBe('BUHAWI')
        ->and($record->amount_assistance)->toBe('100000.00')
        ->and($record->jobs_created)->toBe('10');
});

test('authenticated users can import a project impact workbook sheet', function () {
    $user = User::factory()->create();
    $header = array_fill(0, 22, '');
    $row = [
        '1',
        'BUHAWI',
        'DOST-MIRDC',
        'RDI',
        '',
        '45720',
        '1',
        'Technical mentoring',
        '',
        'PHP 500,000',
        'Local licensing',
        '10',
        'PHP 1,000,000',
        '20% productivity increase',
        'Metro Manila',
        'Women-led MSMEs',
        'Utility model licensed',
        'BUHAWI Technologies',
        '15 engineers trained',
        'Regional recognition',
        'Commercialization initiated.',
        '',
    ];
    $file = UploadedFile::fake()->createWithContent(
        'project-impact.xlsx',
        projectImpactXlsx('TECH TRANS 2021-2025', [$header, $header, $row]),
    );

    $this->actingAs($user)
        ->post(route('admin.project-impact-tracking.import'), ['file' => $file])
        ->assertRedirect(route('admin.project-impact-tracking.index'))
        ->assertSessionHasNoErrors();

    $record = ProjectImpact::query()->firstOrFail();

    expect($record->project_title)->toBe('BUHAWI')
        ->and($record->classification)->toBe('RDI')
        ->and($record->jobs_created)->toBe('10')
        ->and($record->impact_narrative)->toBe('Commercialization initiated.');
});

test('project impact imports reject invalid files', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.project-impact-tracking.import'), [
            'file' => UploadedFile::fake()->createWithContent(
                'project-impact.txt',
                'not a workbook',
            ),
        ])
        ->assertSessionHasErrors('file');

    $file = UploadedFile::fake()->createWithContent(
        'project-impact.csv',
        projectImpactCsv([['Wrong Header'], ['Value']]),
    );

    $this->actingAs($user)
        ->post(route('admin.project-impact-tracking.import'), ['file' => $file])
        ->assertSessionHasErrors('file');
});

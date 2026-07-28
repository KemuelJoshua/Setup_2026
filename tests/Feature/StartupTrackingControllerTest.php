<?php

use App\Models\StartupTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function startupTrackingPayload(array $overrides = []): array
{
    return array_merge([
        'type' => 'PAC',
        'program' => 'TECHNICOM 1.0',
        'project_title' => 'Optimization of an Industry Grade Prototype',
        'proponent_name' => 'Dr. Ria Liza Canlas',
        'contact_details' => 'ria@example.com',
        'amount' => '4999045.00',
        'class' => 'STARTUP',
        'status' => 'ONGOING',
        'promotional_assistance' => 'Marketing Assistance Program',
        'revenue_growth' => '12% annual',
        'jobs_created' => '8',
        'investments_attracted' => 'PHP 2,000,000 seed funding',
        'market_reach' => 'Regional',
        'high_tech_exports' => null,
        'social_impact' => 'Improved access to local technology',
        'next_possible_intervention' => 'International market validation',
    ], $overrides);
}

function startupTrackingHeaders(): array
{
    return [
        'TYPE',
        'Program',
        'Project Title',
        'Name of Proponent',
        'Contact Details',
        'Amount',
        'CLASS',
        'STATUS',
        'Promotional Assistance',
        'Revenue Growth (annual/cumulative)',
        'Jobs Created',
        'Investments Attracted (private co-investment, seed, VC, grants)',
        'Market Reach (local, regional, global)',
        'High-tech Exports (export volume/value) if applicable',
        'Social Impact',
        'Next Possible Intervention',
    ];
}

function startupTrackingCsv(array $rows): string
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

function startupTrackingXlsx(array $rows): string
{
    $temporaryPath = tempnam(sys_get_temp_dir(), 'startup-tracking-');
    $archive = new ZipArchive;
    $archive->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $archive->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets><sheet name="Startups" sheetId="1" r:id="rId1"/></sheets>
</workbook>
XML);
    $archive->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML);

    $xmlRows = collect($rows)->map(function (array $row, int $rowIndex): string {
        $cells = collect($row)->map(function ($value, int $columnIndex) use ($rowIndex): string {
            $column = chr(65 + $columnIndex);
            $escaped = htmlspecialchars((string) $value, ENT_XML1);

            return "<c r=\"{$column}".($rowIndex + 1)."\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
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

test('guests cannot access startup tracking', function () {
    $this->get(route('admin.startup-tracking.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view and filter startup tracking records', function () {
    $user = User::factory()->create();
    StartupTracking::factory()->create([
        'type' => 'PAC',
        'project_title' => 'Deepwater Visual Mapping',
    ]);
    StartupTracking::factory()->create([
        'type' => 'PROMOTION',
        'project_title' => 'Improved KoolGear',
    ]);

    $this->actingAs($user)
        ->get(route('admin.startup-tracking.index', [
            'search' => 'Deepwater',
            'type' => 'PAC',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/startup-tracking/Index')
            ->has('records.data', 1)
            ->where('records.data.0.project_title', 'Deepwater Visual Mapping')
            ->where('filters.type', 'PAC')
            ->has('options.types'));
});

test('authenticated users can create update and delete startup tracking records', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.startup-tracking.store'), startupTrackingPayload())
        ->assertRedirect(route('admin.startup-tracking.index'))
        ->assertSessionHas('success');

    $record = StartupTracking::query()->firstOrFail();

    expect($record->project_title)->toBe('Optimization of an Industry Grade Prototype')
        ->and($record->amount)->toBe('4999045.00');

    $this->actingAs($user)
        ->put(
            route('admin.startup-tracking.update', $record),
            startupTrackingPayload(['status' => 'COMPLETED']),
        )
        ->assertRedirect(route('admin.startup-tracking.index'));

    expect($record->refresh()->status)->toBe('COMPLETED');

    $this->actingAs($user)
        ->delete(route('admin.startup-tracking.destroy', $record))
        ->assertRedirect(route('admin.startup-tracking.index'));

    $this->assertModelMissing($record);
});

test('startup tracking forms validate required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('admin.startup-tracking.index'))
        ->post(route('admin.startup-tracking.store'), [
            'type' => '',
            'program' => '',
            'project_title' => '',
            'amount' => -1,
            'class' => '',
        ])
        ->assertRedirect(route('admin.startup-tracking.index'))
        ->assertSessionHasErrors([
            'type',
            'program',
            'project_title',
            'amount',
            'class',
        ]);
});

test('authenticated users can import startup tracking records from csv', function () {
    $user = User::factory()->create();
    $row = [
        'PROMOTION',
        'GALING',
        'Technology Validation of Pili Seal',
        'Engr. Mark Kennedy E. Bantugon',
        'mark@example.com',
        '349,799.56',
        'STARTUP',
        'ONGOING',
        'Marketing Assistance Program, LUNDUYAN',
        '15%',
        '4',
        'Seed grant',
        'Regional',
        '',
        'Community livelihood',
        'Export readiness',
    ];
    $continuationRow = [
        '',
        'GALING',
        'Improved KoolGear',
        'Mr. Ivan Brent Nara',
        'ivan@example.com',
        '4.975,000.00',
        'STARTUP',
        'ONGOING',
        'Marketing Assistance Program',
        '',
        '',
        '',
        'Local',
        '',
        '',
        '',
    ];
    $resetRow = $continuationRow;
    $resetRow[0] = 'APPROVAL';
    $resetRow[2] = 'Reset Fill-down Type';
    $resetContinuationRow = $continuationRow;
    $resetContinuationRow[2] = 'Inherited Reset Type';
    $file = UploadedFile::fake()->createWithContent(
        'startup-tracking.csv',
        startupTrackingCsv([
            startupTrackingHeaders(),
            $row,
            $continuationRow,
            $resetRow,
            $resetContinuationRow,
        ]),
    );

    $this->actingAs($user)
        ->post(route('admin.startup-tracking.import'), ['file' => $file])
        ->assertRedirect(route('admin.startup-tracking.index'))
        ->assertSessionHas('success', '4 startup tracking records imported successfully.');

    $record = StartupTracking::query()
        ->where('project_title', 'Technology Validation of Pili Seal')
        ->firstOrFail();

    expect($record->project_title)->toBe('Technology Validation of Pili Seal')
        ->and($record->amount)->toBe('349799.56')
        ->and($record->promotional_assistance)->toBe('Marketing Assistance Program, LUNDUYAN')
        ->and(StartupTracking::query()->where('project_title', 'Improved KoolGear')->value('type'))
        ->toBe('PROMOTION')
        ->and(StartupTracking::query()->where('project_title', 'Improved KoolGear')->value('amount'))
        ->toBe('4975000.00')
        ->and(StartupTracking::query()->where('project_title', 'Reset Fill-down Type')->value('type'))
        ->toBe('APPROVAL')
        ->and(StartupTracking::query()->where('project_title', 'Inherited Reset Type')->value('type'))
        ->toBe('APPROVAL');
});

test('authenticated users can import startup tracking records from xlsx', function () {
    $user = User::factory()->create();
    $row = [
        'APPROVAL',
        'VFP',
        'Application for Local and International Certifications',
        'Dr. Raul V. Destura',
        'rvdestura@up.edu.ph',
        '3792164.9199999999',
        'STARTUP',
        'COMPLETED',
        '',
        '',
        '',
        '',
        'Global',
        '',
        '',
        '',
    ];
    $file = UploadedFile::fake()->createWithContent(
        'startup-tracking.xlsx',
        startupTrackingXlsx([startupTrackingHeaders(), $row]),
    );

    $this->actingAs($user)
        ->post(route('admin.startup-tracking.import'), ['file' => $file])
        ->assertRedirect(route('admin.startup-tracking.index'))
        ->assertSessionHasNoErrors();

    expect(StartupTracking::query()->firstOrFail())
        ->project_title->toBe('Application for Local and International Certifications')
        ->status->toBe('COMPLETED')
        ->amount->toBe('3792164.92');
});

test('spreadsheet imports reject invalid files and missing columns', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.startup-tracking.import'), [
            'file' => UploadedFile::fake()->createWithContent('startups.txt', 'not a spreadsheet'),
        ])
        ->assertSessionHasErrors('file');

    $file = UploadedFile::fake()->createWithContent(
        'startups.csv',
        startupTrackingCsv([['TYPE', 'Program'], ['PAC', 'TECHNICOM 1.0']]),
    );

    $this->actingAs($user)
        ->post(route('admin.startup-tracking.import'), ['file' => $file])
        ->assertSessionHasErrors('file');

    $leadingBlankRow = array_values(startupTrackingPayload([
        'type' => '',
    ]));
    $file = UploadedFile::fake()->createWithContent(
        'startups.csv',
        startupTrackingCsv([startupTrackingHeaders(), $leadingBlankRow]),
    );

    $this->actingAs($user)
        ->post(route('admin.startup-tracking.import'), ['file' => $file])
        ->assertSessionHasErrors('file');
});

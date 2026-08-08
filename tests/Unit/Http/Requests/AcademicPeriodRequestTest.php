<?php

use App\Http\Requests\Academics\AcademicPeriodRequest;

test('academic period browser inputs are normalized before validation', function () {
    $request = new class extends AcademicPeriodRequest
    {
        public function normalizeInput(): void
        {
            $this->prepareForValidation();
        }
    };

    $request->replace([
        'academic_term_structure_id' => '12',
        'parent_id' => '34',
        'sequence' => '2',
    ]);

    $request->normalizeInput();

    expect($request->input('academic_term_structure_id'))->toBe(12)
        ->and($request->input('parent_id'))->toBe(34)
        ->and($request->input('sequence'))->toBe(2);
});

test('a missing academic period parent is normalized to null', function () {
    $request = new class extends AcademicPeriodRequest
    {
        public function normalizeInput(): void
        {
            $this->prepareForValidation();
        }
    };

    $request->replace([
        'academic_term_structure_id' => '12',
        'sequence' => '1',
    ]);

    $request->normalizeInput();

    expect($request->input('parent_id'))->toBeNull();
});

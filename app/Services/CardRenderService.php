<?php

namespace App\Services;

use App\Models\CardTemplate;
use App\Models\Student;

class CardRenderService
{
    public function values(Student $student): array
    {
        return [
            'names' => $student->names,
            'lastnames' => $student->lastnames,
            'ci_number' => $student->ci_number,
            'student_code' => $student->student_code,
            'career' => $student->career?->name,
            'semester' => $student->semester?->name,
            'photo' => $student->photo_path,
        ];
    }

    public function objects(CardTemplate $template): array
    {
        $json = json_decode($template->design_json ?: '{}', true);
        return $json['objects'] ?? [];
    }

    public function processContent(string $content, array $vals): string
    {
        $year = date('Y');
        $search = [
            '{{names}}',
            '{{lastnames}}',
            '{{ci_number}}',
            '{{student_code}}',
            '{{career}}',
            '{{semester}}',
            '{{year}}'
        ];
        $replace = [
            $vals['names'] ?? '',
            $vals['lastnames'] ?? '',
            $vals['ci_number'] ?? '',
            $vals['student_code'] ?? '',
            $vals['career'] ?? '',
            $vals['semester'] ?? '',
            $year
        ];
        return str_replace($search, $replace, $content);
    }
}

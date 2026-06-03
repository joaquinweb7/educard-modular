<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PhotoValidationService
{
    public function validate($photoPath)
    {
        $fullPath = Storage::disk('public')->path($photoPath);

        // Path to the python script
        $scriptPath = base_path('scripts/photo_validator.py');
        
        // Determine the Python executable to use
        // In local Windows we use the venv, in Ubuntu we can use `python3` or a venv
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pythonPath = base_path('scripts\venv\Scripts\python.exe');
        } else {
            // For Linux, check if local venv exists, otherwise fallback to system python3
            if (file_exists(base_path('scripts/venv/bin/python'))) {
                $pythonPath = base_path('scripts/venv/bin/python');
            } else {
                $pythonPath = 'python3';
            }
        }

        try {
            $process = new Process([$pythonPath, $scriptPath, $fullPath]);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Photo validation script failed', ['error' => $process->getErrorOutput()]);
                return [
                    'status' => 'manual_review',
                    'details' => ['error' => 'El script de validación falló.', 'raw' => $process->getErrorOutput()]
                ];
            }

            $output = json_decode($process->getOutput(), true);
            
            if (!$output || !isset($output['success'])) {
                Log::error('Invalid output from photo validation script', ['output' => $process->getOutput()]);
                return [
                    'status' => 'manual_review',
                    'details' => ['error' => 'Respuesta no válida del algoritmo.', 'raw' => $process->getOutput()]
                ];
            }

            $status = $output['success'] ? 'passed' : 'failed';
            // Even if it failed mathematically, let's put it as manual review so the admin can verify
            if ($status === 'failed') {
                $status = 'manual_review';
            }

            return [
                'status' => $status,
                'details' => $output['details'] ?? []
            ];

        } catch (\Exception $e) {
            Log::error('Exception running photo validation', ['exception' => $e->getMessage()]);
            return [
                'status' => 'manual_review',
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }
}

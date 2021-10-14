<?php
declare(strict_types=1);

namespace App\Service;

class FileStreamService
{
    /**
     * @param array $emails
     * @param string $filename
     */
    public function output(array $emails, string $filename): void
    {
        $filename .= ".csv";

        $steam = fopen(__DIR__ . "/../../output/$filename", 'w');

        foreach ($emails as $data) {
            if (!is_array($data)) {
                $data = [$data];
            }

            fputcsv($steam, $data);
        }

        fclose($steam);
    }
}

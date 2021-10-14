<?php
declare(strict_types=1);

namespace App\Validator;

use App\Collector;
use App\Service\DomainParserService;
use App\Service\MxService;

class FormatValidator
{
    public function __construct(
        private Collector $collector,
        private DomainParserService $domainParserService,
        private MxService $mxService
    ){}

    public function validate(): void
    {
        foreach ($this->collector->getEmails() as $email) {
            if (!str_contains($email, '@')) {
                $this->collector->addCorruptedEmail($email, 'Invalid format');
            }
        }
    }

    public function __destruct()
    {
        var_dump('destruct');
        $uniqueDomains = $this->domainParserService->getUniqueDomains();
        $this->mxService->addMx($uniqueDomains);
    }
}

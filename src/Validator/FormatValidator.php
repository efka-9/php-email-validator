<?php
declare(strict_types=1);

namespace App\Validator;

use App\Collector;
use App\Service\DomainParserService;
use App\Service\MxService;

class FormatValidator
{
    /**
     * @param Collector $collector
     * @param DomainParserService $domainParserService
     * @param MxService $mxService
     */
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
        $uniqueDomains = $this->domainParserService->getUniqueDomains();

        echo "Checking Mx records ..."> PHP_EOL;

        $this->mxService->addMx($uniqueDomains);
    }
}

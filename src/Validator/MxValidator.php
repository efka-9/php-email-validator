<?php
declare(strict_types=1);

namespace App\Validator;

use App\Collector;
use App\Service\DomainParserService;

class MxValidator
{
    /**
     * @param Collector $collector
     */
    public function __construct(private Collector $collector){}

    public function validate(): void
    {
        foreach ($this->collector->getEmails() as $email) {
            $domain = DomainParserService::staticParser($email);

            $mx = $this->collector->getDomainWithMx()[$domain];

            if (!$mx) {
                $this->collector->addCorruptedEmail($email, 'Mx record is not valid or can not be reached');
            }
        }
    }
}

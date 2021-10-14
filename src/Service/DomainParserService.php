<?php
declare(strict_types=1);

namespace App\Service;

use App\Collector;

class DomainParserService
{
    public function __construct(private Collector $collector){}

    public static function staticParser(string $email): string
    {
        preg_match("/[^@]*$/", $email, $domain);

        return (string)$domain[0];
    }

    public function getUniqueDomains(): array
    {
        $domains = [];

        foreach ($this->collector->getEmails() as $email) {
            $this->sanitize($email);

            $domain = self::staticParser($email);

            if (!isset($domains[$domain])) {
                $domains[] = $domain;
            }
        }

        return $domains;
    }

    private function sanitize(string &$email): void
    {
        $email = str_replace(PHP_EOL, '', $email);
        $email = rtrim($email);
    }
}

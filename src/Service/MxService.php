<?php
declare(strict_types=1);

namespace App\Service;

use App\Collector;
use Symfony\Component\Process\Process;

class MxService
{
    public function __construct(private Collector $collector){}

    public function addMx(array $domains): void
    {
        $domainWithMx = [];

        foreach ($domains as $domain) {
            $process = new Process(['dig', $domain, 'MX', '+short']);
            $process->run();

            $domainWithMx[$domain] = $this->findOneByHighestPriority(explode(PHP_EOL, $process->getOutput()));
        }

        $this->collector->setDomainWithMx($domainWithMx);
    }

    /**
     * @param array $mxs
     * @return string
     */
    private function findOneByHighestPriority(array $mxs): string
    {
        $initPriority = 0;
        $mx = '';

        foreach ($mxs as $item) {
            preg_match("/^\\d*/", $item, $priority);

            if ($priority[0] > $initPriority && !str_contains($item, 'invalid')) {
                $initPriority = $priority[0];
                $mx = str_replace($priority[0], '', $item);
            }
        }

        return ltrim($mx);
    }
}

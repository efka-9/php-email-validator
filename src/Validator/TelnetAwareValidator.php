<?php
declare(strict_types=1);

namespace App\Validator;

use App\Collector;
use App\Exception\BatchCorruptedException;
use App\Service\DomainParserService;

class TelnetAwareValidator
{
    public function __construct(private Collector $collector){}

    public function validate(): void
    {
        foreach ($this->collector->getEmails() as $email) {
            $domain = DomainParserService::staticParser($email);

            try {
                $failed = $this->openTelnetConnection($this->collector->getDomainWithMx()[$domain], $email);

                sleep(1);

                if ($failed) {
                    $this->collector->addCorruptedEmail($email, 'Recipient can not receive email');
                }
            } catch (BatchCorruptedException $e) {
                echo $e->getMessage(). PHP_EOL;

                foreach ($this->collector->getEmails() as $el) {
                    if (str_ends_with($el, $domain)) {
                        $this->collector->addCorruptedEmail($el, $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * @param string $mx
     * @param string $email
     * @return bool
     * @throws BatchCorruptedException
     */
    private function openTelnetConnection(string $mx, string $email): bool
    {
        $fp = fsockopen($mx, 25, $errorNumber, $errorMessage);

        if (!$fp) {
            throw new BatchCorruptedException("Can not establish connection between smtp server $errorMessage");
        }

        fputs($fp, "EHLO protonmail.com". PHP_EOL);
        fputs($fp, "mail from:<evaldas.butkus@protonmail.com>". PHP_EOL);
        fputs($fp, "rcpt to:<$email>". PHP_EOL);
        fputs($fp, "quit". PHP_EOL);

        $stop = false;

        while(!feof($fp) && !$stop) {
            $stdout = fgets($fp, 4096);
            $stdout = rtrim($stdout);

            if (str_starts_with($stdout, '550') && $stdout) {
                $corruptedEmails[] = $email;
                $stop = true;
            }

            if (str_contains($stdout, 'Unrecognized command')) {
                $stop = true;
            }
        }

        fclose($fp);

        return $stop;
    }
}

<?php
declare(strict_types=1);

namespace App;

use App\Service\FileStreamService;

class Collector
{
    private array $emails = [];
    private array $corruptedEmails = [];
    private array $domainWithMx = [];

    public function __construct(
        private FileStreamService $fileStreamService,
        private string $filename
    ) {
        $this->stdinStream();
    }

    private function stdinStream(): void
    {
        while ($line = fgets(STDIN)) {
            $this->emails[] = rtrim($line);
        }
    }

    public function addCorruptedEmail(string $email, string $reason)
    {
        $key = array_search($email, $this->emails);
        unset($this->emails[$key]);

        $this->corruptedEmails[] = [
            'email' => $email,
            'reason' => $reason,
        ];
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getCorruptedEmails(): array
    {
        return $this->corruptedEmails;
    }

    public function getDomainWithMx(): array
    {
        return $this->domainWithMx;
    }

    public function setDomainWithMx(array $domainWithMx): self
    {
        $this->domainWithMx = $domainWithMx;

        return $this;
    }

    public function __destruct()
    {
        $this->fileStreamService->output($this->emails, "{$this->filename}_clean");
        $this->fileStreamService->output($this->corruptedEmails, "{$this->filename}_corrupted");
    }
}

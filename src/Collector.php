<?php
declare(strict_types=1);

namespace App;

use App\Service\FileStreamService;

class Collector
{
    /**
     * @var array
     */
    private array $emails = [];
    /**
     * @var array
     */
    private array $corruptedEmails = [];
    /**
     * @var array
     */
    private array $domainWithMx = [];

    /**
     * @param FileStreamService $fileStreamService
     * @param string $filename
     */
    public function __construct(
        private FileStreamService $fileStreamService,
        private string $filename
    ) {
        $this->stdinStream();
    }

    /**
     *
     */
    private function stdinStream(): void
    {
        while ($line = fgets(STDIN)) {
            $this->emails[] = rtrim($line);
        }
    }

    /**
     * @param string $email
     * @param string $reason
     */
    public function addCorruptedEmail(string $email, string $reason)
    {
        $key = array_search($email, $this->emails);
        unset($this->emails[$key]);

        $this->corruptedEmails[] = [
            'email' => $email,
            'reason' => $reason,
        ];
    }

    /**
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

    /**
     * @return array
     */
    public function getCorruptedEmails(): array
    {
        return $this->corruptedEmails;
    }

    /**
     * @return array
     */
    public function getDomainWithMx(): array
    {
        return $this->domainWithMx;
    }

    /**
     * @param array $domainWithMx
     * @return $this
     */
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

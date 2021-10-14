<?php
declare(strict_types=1);

namespace App\DependencyInjection;

use App\Collector;
use App\Service\DomainParserService;
use App\Service\FileStreamService;
use App\Service\MxService;
use App\Validator\FormatValidator;
use App\Validator\MxValidator;
use App\Validator\TelnetAwareValidator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['filename'] = fn() => $GLOBALS['argv'][1] ?? throw new \Exception('Filename is missing');
        $pimple[FileStreamService::class] = fn(): FileStreamService => new FileStreamService();
        $pimple[Collector::class] = fn(): Collector => new Collector($pimple[FileStreamService::class], $pimple['filename']);
        $pimple[DomainParserService::class] = fn(): DomainParserService => new DomainParserService($pimple[Collector::class]);
        $pimple[MxService::class] = fn(): MxService => new MxService($pimple[Collector::class]);
        $pimple[FormatValidator::class] = fn(): FormatValidator => new FormatValidator(
            $pimple[Collector::class],
            $pimple[DomainParserService::class],
            $pimple[MxService::class]
        );
        $pimple[MxValidator::class] = fn(): MxValidator => new MxValidator($pimple[Collector::class]);
        $pimple[TelnetAwareValidator::class] = fn(): TelnetAwareValidator => new TelnetAwareValidator($pimple[Collector::class]);
    }
}

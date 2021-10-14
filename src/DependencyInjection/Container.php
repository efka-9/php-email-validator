<?php
declare(strict_types=1);

namespace App\DependencyInjection;

use App\Collector;
use App\Service\DomainParserService;
use App\Service\FileStreamService;
use App\Service\MxService;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Container
{
    public static function touch(): \DI\Container
    {
        static $container = null;

        if (null == $container) {
            $builder = new ContainerBuilder();

            $builder->addDefinitions([
                'filename' => fn() => $GLOBALS['argv'][1] ?? throw new \Exception('Filename is missing'),
                FileStreamService::class => fn() => new FileStreamService(),
                Collector::class => fn(ContainerInterface $container) => new Collector($container->get(FileStreamService::class), $container->get('filename')),
                DomainParserService::class => fn(ContainerInterface $container) => new DomainParserService($container->get(Collector::class)),
                MxService::class => fn(ContainerInterface $container) => new MxService($container->get(Collector::class)),
            ]);

            $container = $builder->build();
        }

        return $container;
    }
}

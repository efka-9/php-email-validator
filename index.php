<?php
declare(strict_types=1);

use App\Collector;
use App\DependencyInjection\Container;
use App\Service\DomainParserService;
use App\Service\MxService;
use App\Validator\FormatValidator;
use App\Validator\MxValidator;
use App\Validator\TelnetAwareValidator;

require_once __DIR__. '/vendor/autoload.php';

$collector = Container::touch()->get(Collector::class);
$domainParserService = Container::touch()->get(DomainParserService::class);
$mxService = Container::touch()->get(MxService::class);

(new FormatValidator($collector, $domainParserService, $mxService))->validate();
(new MxValidator($collector))->validate();
(new TelnetAwareValidator($collector))->validate();

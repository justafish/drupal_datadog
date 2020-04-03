<?php

namespace Drupal\datadog;

use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class DatadogServiceProvider extends ServiceProviderBase implements ServiceProviderInterface {
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('logs_http.logs_http_logger');
    $definition->setClass('Drupal\datadog\DatadogLogsHttpLogger');
  }
}

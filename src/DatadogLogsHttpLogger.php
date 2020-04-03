<?php

namespace Drupal\Datadog;

use Drupal\logs_http\Logger\LogsHttpLogger;

class DatadogLogsHttpLogger extends LogsHttpLogger {

  /**
   * {@inheritdoc}
   */
  public function registerEvent($level, $message, array $context = []) {
    if (!$this->isEnabled()) {
      return;
    }

    // Populate the message placeholders and then replace them in the message.
    $message_placeholders = $this->logMessageParser->parseMessagePlaceholders($message, $context);
    $message = empty($message_placeholders) ? $message : strtr($message, $message_placeholders);
    $referer = parse_url($context['referer']);

    $event = [
      'source' => 'php',
      'level' => $level,
      'level_name' => $this->severityLevels[$level]->getUntranslatedString(),
      'host' => isset($referer['host']) ? $referer['host'] : NULL,
      'http' => [
        'url' => $context['request_uri'],
        'referer' => $context['referer'],
      ],
      'network' => [
        'ip' => $context['ip'],
      ],
      'message' => $message,
      'context' => [
        'user' => $context['uid'],
        'timestamp' => $context['timestamp'],
      ],
    ];

    if (!empty($context['exception_trace'])) {
      // We avoid unserializing as it seems to causes Logs to fail to index
      // event as JSON.
      $event['exception_trace'] = base64_decode($context['exception_trace']);
    }

    if ($uuid = $this->config->get('uuid')) {
      $event['service'] = $uuid;
    }

    // Remove empty values, to prevent errors in the indexing of the JSON.
    $event = $this->arrayRemoveEmpty($event);

    // Prevent identical events.
    $event_clone = $event;
    unset($event_clone['timestamp']);
    $key = md5(serialize($event_clone));
    $this->cache[$key] = $event;
  }
}

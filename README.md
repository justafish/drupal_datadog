# DataDog Logs HTTP

Overrides the data format used by [Logs HTTP](https://www.drupal.org/project/logs_http)
to be compatible with DataDog's log intake endpoint.

You can set your service name with the Logs HTTP UI, or override it by setting
`$settings['datadog_service'] = 'drupal-local';` in `settings.php`
(See https://github.com/Gizra/logs_http/issues/24)
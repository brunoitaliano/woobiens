<?php

namespace Drupal\httpswww\EventSubscriber;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HttpsWwwRedirectSubscriber.
 *
 * @package Drupal\httpswww\EventSubscriber
 */
class HttpsWwwRedirectSubscriber implements EventSubscriberInterface {

  /**
   * Module specific configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * HttpsWwwRedirectSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config.
   * @param \Drupal\Core\Session\AccountInterface      $account
   *   The current user.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountInterface $account) {
    $this->config = $config_factory->get('httpswww.settings');
    $this->account = $account;
  }

  /**
   * Executes a redirect if one is needed based on config.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The event to process.
   */
  public function redirect(GetResponseEvent $event) {

    // Quit if it's not enabled.
    if (empty($this->config->get('enabled'))) {
      return;
    }

    // Quit if the user has the bypass permission.
    if ($this->account->hasPermission('bypass httpswww redirect')) {
      return;
    }

    $host = $event->getRequest()->getHost();
    $scheme = $event->getRequest()->getScheme();

    $conf_scheme = $this->config->get('scheme') ?: 'mixed';
    $use_prefix = $this->config->get('prefix') == 'yes';

    $curr_url = $event->getRequest()->getSchemeAndHttpHost();

    // Set scheme.
    $new_url = ($conf_scheme === 'mixed' ? $scheme : $conf_scheme) . '://';

    // Set/remove prefix and add host.
    if ($use_prefix && substr($host, 0, 4) !== 'www.') {
      $new_url .= 'www.' . $host;
    }
    elseif (!$use_prefix && substr($host, 0, 4) === 'www.') {
      $new_url .= substr($host, 4);
    }
    else {
      $new_url .= $host;
    }

    // Check if the URL is valid and redirect if URLs doesn't match.
    if (UrlHelper::isValid($new_url, TRUE) && $curr_url !== $new_url) {
      $new_url .= $event->getRequest()->getRequestUri();
      $response = new TrustedRedirectResponse($new_url, 301);

      $build = [
        '#cache' => [
          'max-age'  => 0,
          'contexts' => ['url', 'user.permissions'],
          'tags'     => ['config:httpswww.settings'],
        ],
      ];
      $cache_meta = CacheableMetadata::createFromRenderArray($build);
      $response->addCacheableDependency($cache_meta);
      $event->setResponse($response);
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Set this to run as early as possible, but after the user authentication.
    $events[KernelEvents::REQUEST][] = ['redirect', 299];
    return $events;
  }

}

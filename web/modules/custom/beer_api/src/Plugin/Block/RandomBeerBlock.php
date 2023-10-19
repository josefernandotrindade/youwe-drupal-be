<?php

namespace Drupal\beer_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\beer_api\PunkApiInterface;

/**
 * Provides a 'Random Beer' Block.
 *
 * @Block(
 *   id = "random_beer_block",
 *   admin_label = @Translation("Random Beer block"),
 *   category = @Translation("Beer"),
 * )
 */
class RandomBeerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,$plugin_id, $plugin_definition, ClientInterface $httpClient) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->httpClient = $httpClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $request = $this->httpClient->request('GET',  PunkApiInterface::RANDOM_BEER_ENDPOINT, [
      'http_errors' => FALSE, // Don't throw exceptions.
    ]);

    if ($request->getStatusCode() == 200) {
      $response = json_decode($request->getBody()->getContents());
    }

    if (isset($response) && is_array($response)) {
      $beer = reset($response);
    }

    if (!empty($beer)) {
      return [
        '#theme' => 'beer_card',
        '#name' => $beer->name,
        '#tagline' => $beer->tagline,
        '#abv' => $beer->abv,
        '#image' => $beer->image_url,
      ];
    }

    return [
      '#markup' => $this->t('No beer was found'),
    ];
  }
}

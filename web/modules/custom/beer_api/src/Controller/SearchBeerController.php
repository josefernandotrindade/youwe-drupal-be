<?php

namespace Drupal\beer_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\beer_api\PunkApiInterface;

class SearchBeerController extends ControllerBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * Restrict search results to MAX_SEARCH_RESULTS items.
   */
  const MAX_SEARCH_RESULTS = 3;

  /**
   * SearchBeerController constructor.
   *
   * @param use GuzzleHttp\ClientInterface $httpClient
   */
  public function __construct(ClientInterface $httpClient) {
    $this->httpClient = $httpClient;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  /**
   * Search for beers matching the current dish.
   */
  public function content(Request $request) {
    $dish = trim($request->query->get('dish'));

    $build = [];

    $request = $this->httpClient->request('GET', PunkApiInterface::SEARCH_BEER_ENDPOINT, [
      'query' => [
        'per_page' => self::MAX_SEARCH_RESULTS,
        'food' => $dish,
      ],
      'http_errors' => FALSE,
    ]);

    if ($dish) {
      $build[] = [
        '#markup' => $this->t('Results for beers matching %dish.', [
          '%dish' => $dish,
        ]),
      ];
    }

    if ($request->getStatusCode() == 200) {
      $beers = json_decode($request->getBody()->getContents());

      if (is_array($beers) && count($beers) > 0) {
        foreach ($beers as $beer) {
          $build[] = [
            '#theme' => 'beer_card',
            '#name' => $beer->name,
            '#tagline' => $beer->tagline,
            '#abv' => $beer->abv,
            '#image' => $beer->image_url,
          ];
        }
      }
      else {
        $build[] = [
          '#markup' => $this->t('Sorry, no beers were found.'),
        ];
      }
    }

    $build['#cache'] = [
      'contexts' => [
        'url.query_args:dish', // This won't work in the internal page cache module is enabled.
      ],
    ];

    return $build;
  }
}

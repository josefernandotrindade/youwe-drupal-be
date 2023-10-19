<?php

namespace Drupal\beer_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Search Beer' Block.
 *
 * @Block(
 *   id = "search_beer_block",
 *   admin_label = @Translation("Search Beer block"),
 *   category = @Translation("Beer"),
 * )
 */
class SearchBeerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Form\FormBuilderInterface definition.
   *
   * @var FormBuilderInterface formBuilder
   */
  protected FormBuilderInterface $formBuilder;

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var RequestStack requestStack
   */
  protected RequestStack $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,$plugin_id, $plugin_definition, FormBuilderInterface $formBuilder, RequestStack $requestStack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->formBuilder = $formBuilder;
    $this->requestStack = $requestStack;
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
      $container->get('form_builder'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $request = $this->requestStack?->getCurrentRequest();
    $dish = NULL;

    if ($request) {
      $dish = $request->query->get('dish');
    }

    return $this->formBuilder->getForm('Drupal\beer_api\Form\SearchBeerForm', [
      'dish' => $dish,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), [
      'url.query_args:dish', // Won't work unless IPC is disabled.
    ]);
  }
}

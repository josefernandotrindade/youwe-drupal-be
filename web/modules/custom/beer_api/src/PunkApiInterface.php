<?php

namespace Drupal\beer_api;

/**
 * Just so we don't hardcode URLs in the code.
 *
 * @see https://punkapi.com/documentation/v2
 */
interface PunkApiInterface {

  /**
   * Get one random beer.
   */
  const RANDOM_BEER_ENDPOINT = 'https://api.punkapi.com/v2/beers/random';

  /**
   * Search for beers.
   */
  const SEARCH_BEER_ENDPOINT = 'https://api.punkapi.com/v2/beers';
}

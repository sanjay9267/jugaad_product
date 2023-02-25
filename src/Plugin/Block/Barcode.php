<?php

namespace Drupal\jugaad_product\Plugin\Block;

use Com\Tecnick\Barcode\Barcode as BarcodeGenerator;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;
use Symfony\Component\HttpFoundation\Request;
//require ('autoload.php');

/**
 * Provides a 'Barcode' block.
 *
 * @Block(
 *  id = "barcode",
 *  admin_label = @Translation("Barcode"),
 * )
 */
class Barcode extends BlockBase{
  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::request()->attributes->get('node');
    $field_app_purchase_link = $node->get('field_app_purchase_link')->getString();
    $generator = new BarcodeGenerator();
    
    // generate a barcode
    $barcode = $generator->getBarcodeObj(
      'QRCODE',                     // barcode type and additional comma-separated parameters
      $field_app_purchase_link,          // data string to encode
      -4,                             // bar width (use absolute or negative value as multiplication factor)
      -4,                             // bar height (use absolute or negative value as multiplication factor)
      'black',                        // foreground color
      array(-2, -2, -2, -2)           // padding (use absolute or negative values as multiplication factors)
    ); // background color

    return [
      '#theme' => 'barcode__qrcode',
      '#barcode' => $barcode->getHtmlDiv(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id() . $field_app_purchase_link));
    } else {
      return parent::getCacheTags();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}
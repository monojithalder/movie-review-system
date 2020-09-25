<?php
namespace Drupal\views_general\Plugin\views\field;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Class Avatar
 *
 * @ViewsField("standard")
 */
class Standard extends FieldPluginBase {
  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    return check_markup($values, 'full_html');
  }
}

<?php


namespace LastCall\Patterns\Core\Render;


use LastCall\Patterns\Core\Pattern\PatternInterface;
use Pimple\Container;
use Symfony\Component\Templating\EngineInterface;

final class Renderer {

  /**
   * @var \Symfony\Component\Templating\EngineInterface
   */
  private $engine;

  public function __construct(EngineInterface $engine) {
    $this->engine = $engine;
  }

  public function render(PatternInterface $pattern): RenderedInterface {
    $rendered = new Rendered($pattern);
    $vars = $this->extractVars($pattern);
    $markup = $this->engine->render($pattern->getTemplateReference(), $vars);
    $rendered->setMarkup($markup);
    return $rendered;
  }

  private function extractVars(PatternInterface $pattern) {
    /** @var \Pimple\Container $vars */
    $vars = $pattern->getTemplateVariables();

    if($vars && $vars instanceof Container) {
      $_vars = [];
      foreach($vars->keys() as $k) {
        $_vars[$k] = $vars[$k];
      }
      return $_vars;
    }

    return $vars;
  }
}
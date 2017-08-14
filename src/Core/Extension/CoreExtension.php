<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Extension;

use LastCall\Mannequin\Core\Subscriber\CssJsResolverSubscriber;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\NestedAssetSubscriber;
use LastCall\Mannequin\Core\Subscriber\VariableResolverSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CoreExtension extends AbstractExtension implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            $this->getPatternExpressionFunction(),
            $this->getMarkupExpressionFunction(),
            $this->getAssetExpressionFunction(),
        ];
    }

    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new YamlFileMetadataSubscriber($this->mannequin->getMetadataParser()));
        $dispatcher->addSubscriber(new LastChanceNameSubscriber());
        $dispatcher->addSubscriber(new VariableResolverSubscriber($this->mannequin->getVariableResolver()));
        $dispatcher->addSubscriber(new CssJsResolverSubscriber($this->mannequin->getAssetFactory(), $this->mannequin->getUrlGenerator()));
        $dispatcher->addSubscriber(new NestedAssetSubscriber());
    }

    private function getPatternExpressionFunction()
    {
        return new ExpressionFunction('pattern', function ($arguments, $pid) {
            throw new \ErrorException('Pattern expressions cannot yet be compiled.');
        }, function ($context, $pid) {
            /** @var \LastCall\Mannequin\Core\Pattern\PatternCollection $collection */
            $collection = $context['collection'];
            $pattern = $collection->get($pid);
            $variant = reset($pattern->getVariants());
            $renderer = $this->mannequin->getRenderer();

            return $renderer->render($collection, $pattern, $variant);
        });
    }

    private function getMarkupExpressionFunction()
    {
        return new ExpressionFunction('markup', function () {
            throw new \ErrorException('Markup expressions cannot be compiled.');
        }, function ($args, $markup) {
            $rendered = new Rendered();
            $rendered->setMarkup($markup);

            return $rendered;
        });
    }

    private function getAssetExpressionFunction()
    {
        return new ExpressionFunction('asset', function () {
            throw new \ErrorException('Asset expressions cannot be compiled.');
        }, function ($context, $spec) {
            $asset = $this->mannequin->getAssetFactory()->createAsset($spec, [], [
                'output' => 'assets/*',
            ]);
            /** @var Rendered $rendered */
            $rendered = $context['rendered'];
            $rendered->getAssets()->add($asset);

            return $this->mannequin['url_generator']->generate(
                'static',
                ['name' => $asset->getTargetPath()],
                UrlGeneratorInterface::RELATIVE_PATH
            );
        });
    }
}

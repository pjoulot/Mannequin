<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Subscriber;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Detects used patterns based on variables that are used in the sets.
 */
class NestedPatternVariableSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::DISCOVER => ['detectNestedPatterns', -50],
        ];
    }

    public function detectNestedPatterns(PatternDiscoveryEvent $event)
    {
        $pattern = $event->getPattern();
        $collection = $event->getCollection();
        $definition = $pattern->getVariableDefinition();
        $sets = $pattern->getVariableSets();

        foreach ($definition->keys() as $varName) {
            if ('pattern' === $definition->get($varName)) {
                foreach ($sets as $set) {
                    if ($set->has($varName)) {
                        $id = $set->get($varName);
                        if ($nested = $collection->get($id)) {
                            $pattern->addUsedPattern($nested);
                        }
                    }
                }
            }
        }
    }
}
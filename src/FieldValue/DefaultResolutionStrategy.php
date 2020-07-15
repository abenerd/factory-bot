<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/factory-bot
 */

namespace Ergebnis\FactoryBot\FieldValue;

use Ergebnis\FactoryBot\FieldDefinition;
use Ergebnis\FactoryBot\FixtureFactory;
use Faker\Generator;

final class DefaultResolutionStrategy implements ResolutionStrategy
{
    public function resolve(
        FieldDefinition\Resolvable $fieldDefinition,
        Generator $faker,
        FixtureFactory $fixtureFactory
    ) {
        if ($fieldDefinition instanceof FieldDefinition\Optional && !$faker->boolean()) {
            return null;
        }

        return $fieldDefinition->resolve(
            $faker,
            $fixtureFactory
        );
    }
}

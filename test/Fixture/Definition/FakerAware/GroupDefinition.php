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

namespace Ergebnis\FactoryBot\Test\Fixture\Definition\FakerAware;

use Ergebnis\FactoryBot\Definition\FakerAwareDefinition;
use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use Faker\Generator;

final class GroupDefinition implements FakerAwareDefinition
{
    /**
     * @var null|Generator
     */
    private $faker;

    public function accept(FixtureFactory $factory): void
    {
        $factory->defineEntity(Entity\Group::class);
    }

    public function provideWith(Generator $faker): void
    {
        $this->faker = $faker;
    }

    public function faker(): Generator
    {
        if (null === $this->faker) {
            throw new \RuntimeException(\sprintf(
                'An instance of "%s" has not been provided yet.',
                Generator::class
            ));
        }

        return $this->faker;
    }
}

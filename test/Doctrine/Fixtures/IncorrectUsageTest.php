<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use Ergebnis\FactoryBot\Test\Unit\AbstractTestCase;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class IncorrectUsageTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function throwsWhenTryingToDefineTheSameEntityTwice()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEvenClasses()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity('NotAClass');
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEntities()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->assertTrue(class_exists(Entity\NotAnEntity::class, true));

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Entity\NotAnEntity::class);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToDefineNonexistentFields()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $this->expectException(\Exception::class);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGiveNonexistentFieldsWhileConstructing()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $fixtureFactory->get(Entity\SpaceShip::class, [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGetLessThanOneInstance()
    {
        $fixtureFactory = new FixtureFactory(self::createEntityManager());

        $fixtureFactory->defineEntity(Entity\SpaceShip::class);

        $this->expectException(\Exception::class);

        $fixtureFactory->getList(Entity\SpaceShip::class, [], 0);
    }
}

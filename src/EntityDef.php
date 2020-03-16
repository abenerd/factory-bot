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

namespace Ergebnis\FactoryBot;

use Doctrine\ORM;

/**
 * @internal
 */
final class EntityDef
{
    /**
     * @var ORM\Mapping\ClassMetadata
     */
    private $classMetadata;

    private $fieldDefinitions;

    private $configuration;

    public function __construct(ORM\Mapping\ClassMetadata $classMetadata, array $fieldDefinitions, array $configuration)
    {
        $this->classMetadata = $classMetadata;
        $this->fieldDefinitions = [];
        $this->configuration = $configuration;

        $this->normalizeFieldDefinitions($fieldDefinitions);
        $this->collectDefaultFieldDefinitionsFromClassMetadata();
    }

    /**
     * Returns the fully qualified name of the entity class.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->classMetadata->getName();
    }

    /**
     * Returns the fielde definition callbacks.
     */
    public function getFieldDefinitions()
    {
        return $this->fieldDefinitions;
    }

    /**
     * Returns the Doctrine metadata for the entity to be created.
     *
     * @return ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }

    /**
     * Returns the extra configuration array of the entity definition.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    private function normalizeFieldDefinitions(array $fieldDefinitions): void
    {
        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            if (!$this->classMetadata->hasField($fieldName) && !$this->classMetadata->hasAssociation($fieldName)) {
                throw new \Exception(\sprintf(
                    'No such field in %s: %s',
                    $this->getClassName(),
                    $fieldName
                ));
            }

            $this->fieldDefinitions[$fieldName] = $this->normalizeFieldDefinition($fieldDefinition);
        }
    }

    private function collectDefaultFieldDefinitionsFromClassMetadata(): void
    {
        $defaultEntity = $this->getClassMetadata()->newInstance();

        $fieldNames = \array_merge(
            $this->classMetadata->getFieldNames(),
            $this->classMetadata->getAssociationNames()
        );

        foreach ($fieldNames as $fieldName) {
            if (!isset($this->fieldDefinitions[$fieldName])) {
                $defaultFieldValue = $this->classMetadata->getFieldValue($defaultEntity, $fieldName);

                if (null !== $defaultFieldValue) {
                    $this->fieldDefinitions[$fieldName] = static function () use ($defaultFieldValue) {
                        return $defaultFieldValue;
                    };
                } else {
                    $this->fieldDefinitions[$fieldName] = static function () {
                        return null;
                    };
                }
            }
        }
    }

    private function normalizeFieldDefinition($fieldDefinition)
    {
        if (\is_callable($fieldDefinition)) {
            return $this->ensureInvokable($fieldDefinition);
        }

        return static function () use ($fieldDefinition) {
            return $fieldDefinition;
        };
    }

    private function ensureInvokable($fieldDefinition)
    {
        if (\method_exists($fieldDefinition, '__invoke')) {
            return $fieldDefinition;
        }

        return static function () use ($fieldDefinition) {
            return \call_user_func_array($fieldDefinition, \func_get_args());
        };
    }
}

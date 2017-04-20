<?php
/**
 * Copyright (c) 2013 Adam L. Englander
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace DerpTest\Behat\MachinistExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenario;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use DerpTest\Machinist\Machinist;
use DerpTest\Machinist\Store\Doctrine as DoctrineStore;
use Doctrine\ORM\EntityManager;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Machinist context for implementing Machinist machines in Behat
 */
class RawMachinistContext implements Context, MachinistAwareInterface, KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var Context
     */
    protected $parentContext;

    /**
     * @var \DerpTest\Machinist\Machinist
     */
    protected $machinist;

    /**
     * @var bool
     */
    protected $truncateOnWipe = false;

    /**
     * @var bool
     */
    protected $doctrineOrm = false;

    /**
     * @var EntityManager
     */
    protected $doctrineEntityManager;

    /**
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public function setupDoctrine(BeforeScenarioScope $scope)
    {
        if (!$this->doctrineEntityManager) {
            $this->doctrineEntityManager = $this->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
            $store = new DoctrineStore(
                $this->doctrineEntityManager,
                array('\\AppBundle\\Entity')
            );
            $machinist = $this->getMachinist();
            $machinist->addStore($store, 'default');

            $metaDataFactory = $this->doctrineEntityManager->getMetadataFactory();
            $mappings = [];
            foreach ($metaDataFactory->getAllMetadata() as $metaData) {
                if (!$metaData->isMappedSuperclass) {
                    $mName = $metaData->getName();
                    $mappings[$mName] = [];
                    foreach ($metaData->getAssociationMappings() as $associationMapping) {
                        if ($associationMapping['isOwningSide'] &&
                            !DoctrineStore::isManyToMany($associationMapping['type'])
                        ) {
                            $mappings[$mName][$associationMapping['targetEntity']] = $associationMapping;
                        }
                    }
                    Machinist::blueprint($mName);
                }
            }
            foreach ($mappings as $mName => $mapping) {
                foreach($mapping as $relationship => $associationMapping) {
                    $rel = Machinist::relationship($relationship);
                    $joinColumn = $associationMapping['fieldName'];
                    if (!empty($associationMapping['joinColumns'][0]['name'])) {
                        $joinColumn = $associationMapping['joinColumns'][0]['name'];
                    }
                    $rel->local($associationMapping['fieldName']);
                    $rel->type($associationMapping['type']);
                    Machinist::blueprint($mName)->addDefault(
                        $relationship,
                        $rel
                    );
                }
            }
        }
//
//        /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $env */
//        $env = $scope->getEnvironment();
//        $machinistContextClass = '\DerpTest\Behat\MachinistExtension\Context\MachinistContext';
//        if (!$env->hasContextClass($machinistContextClass)) {
//            $env->registerContext($this->machinistContext);
//        }
    }

    /**
     * Sets parent context of current context.
     *
     * @param Context $parentContext
     */
    public function setParentContext(Context $parentContext)
    {
        $this->parentContext = $parentContext;
    }

    /**
     * Find current context's sub-context by alias name.
     *
     * @param string $alias
     *
     * @return Context
     */
    public function getSubcontext($alias)
    {
        return null;
    }

    /**
     * Returns all added sub-contexts.
     *
     * @return array
     */
    public function getSubcontexts()
    {
        return array();
    }

    /**
     * Finds sub-context by it's name.
     *
     * @param string $className
     *
     * @return Context
     */
    public function getSubcontextByClassName($className)
    {
        return null;
    }

    /**
     * @return Machinist
     */
    public function getMachinist()
    {
        return $this->machinist;
    }

    protected function processParameters(array $parameters)
    {
        if (array_key_exists('truncate_on_wipe', $parameters)) {
            $this->truncateOnWipe = (bool)$parameters['truncate_on_wipe'];
        }
        if (array_key_exists('doctrine_orm', $parameters)) {
            $this->doctrineOrm = true;
        }
    }

    /**
     * Set Machinist
     *
     * @param Machinist $machinist
     * @return void
     */
    public function setMachinist(Machinist $machinist)
    {
        $this->machinist = $machinist;
    }

    /**
     * Set the Machinist parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setMachinistParameters(array $parameters)
    {
        $this->processParameters($parameters);
    }
}

<?php

require_once(__DIR__ . '/bootstrap.php');

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenario;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use DerpTest\Behat\MachinistExtension\Context\MachinistAwareInterface;
use DerpTest\Behat\MachinistExtension\Context\MachinistContext;
use DerpTest\Machinist\Machinist;

/**
 * Features context.
 * this could extend MachinistContext and be done or you can do something similar to below in $this->gatherContexts
 */
class FeatureContext implements MachinistAwareInterface, Context
{
    /**
     * @var \DerpTest\Machinist\Machinist
     */
    private $machinist;

    /**
     * @var MachinistContext
     */
    private $machinistContext;

    /**
     * @var array
     */
    private $machinistParameters;

    /**
     * FeatureContext constructor.
     */
    public function __construct()
    {
        $this->machinistContext = new MachinistContext();
    }

    /**
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public function gatherContexts(BeforeScenarioScope $scope) {
        /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $env */
        $env = $scope->getEnvironment();
        $machinistContextClass = '\DerpTest\Behat\MachinistExtension\Context\MachinistContext';
        if (!$env->hasContextClass($machinistContextClass)) {
            $env->registerContext($this->machinistContext);
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
        $this->machinistContext->setMachinist($machinist);
    }

    /**
     * Set the Machinist parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setMachinistParameters(array $parameters)
    {
        $this->machinistParameters = $parameters;
        $this->machinistContext->setMachinistParameters($parameters);
    }


    /**
     * @BeforeScenario
     */
    public function clearAllData()
    {
        $this->machinist->wipeAll($this->machinistParameters['truncate_on_wipe']);
    }

    /**
     * @Then /^"(?P<property>(?:[^"]|\\")*)" is embedded object in "(?P<blueprint>(?:[^"]|\\")*)" for:$/
     */
    public function isEmbeddedInFor($property, $arg2, TableNode $table)
    {
        throw new PendingException();
    }
}

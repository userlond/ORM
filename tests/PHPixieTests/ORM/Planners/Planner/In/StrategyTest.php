<?php

namespace PHPixieTests\ORM\Planners\Planner\In;

/**
 * @coversDefaultClass \PHPixie\ORM\Planners\Planner\Planner\In\Strategy
 */
abstract class StrategyTest extends \PHPixieTests\AbstractORMTest
{
    protected $steps;
    protected $strategy;
    
    protected $query;
    protected $subquery;
    protected $plan;
    protected $builder;
    
    public function setUp()
    {
        $this->query = $this->getQuery();
        $this->subquery = $this->abstractMock('\PHPixie\Database\Query\Type\Select');
        $this->plan = $this->quickMock('\PHPixie\ORM\Plans\Plan\Steps');
        
        $this->steps = $this->quickMock('\PHPixie\ORM\Steps');
        $this->strategy = $this->getStrategy();
    }
    
    abstract protected function getQuery();
    abstract public function testIn();
    abstract protected function getStrategy();
}

<?php

namespace \PHPixie\ORM\Planners\Planner;

class Update extends \PHPixie\ORM\Planners\Planner
{
    public function plan($ormQuery, $data, $plan)
    {
        $requiresSteps = false;
        foreach ($data as $field => $value) {
            if ($value instanceof Update\Field) {
                $valueField = $value->valueField();
                $valueSource = $value->valueSource();

                if ($valueSource isntanceof \PHPixie\ORM\Model) {
                    $data[$field] = $valueSource->$valueField;
                } else {
                    $requiresSteps = true;
                    $data[$field] = $this->subqueryField($ormQuery, $valueSource, $valueField, $plan);
                }
            }
        }

        if ($requiresSteps) {
            $plan->push($this->steps->update($ormQuery, $data));
        }else
            $plan->appendPlan($ormQuery->updatePlan($data));
    }

    public function field($valueSource, $valueField)
    {
        return new \PHPixie\ORM\Planners\Planner\Update\Field($valueSource, $valueField);
    }

    protected function subqueryField($query, $subquery, $subqueryField, $plan)
    {
        $queryConnection = $query->repository()->connection();
        $subqueryConnection = $subquery->repository()->connection();

        $subplan = $subquery->findPlan();
        $plan->appendPlan($subplan->requiredPlan());
        $fieldSubquery = $subplan->resultStep()->query()->fields(array($valueField));

        if ($queryConnection instanceof PHPixie\DB\Driver\PDO\Connection && $queryConnection === $subqueryConnection)
            return $fieldSubquery;

        $resultStep = $this->steps->resultQuery($fieldSubquery);
        $plan->push($resultStep);

        return $resultStep;
    }
}
<?php

namespace PDSSUtilities;

use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Utilidades para aplicar filtros en un Query dependiendo de los parámetros de la solicitud http
 */
class QueryFilter
{

    const CONDITION_LIKE = 'LIKE';
    const CONDITION_NOT_LIKE = 'NOT_LIKE';
    const CONDITION_BETWEEN = 'BETWEEN';
    const CONDITION_EQUAL = 'EQUAL';
    const CONDITION_NOT_EQUAL = 'NOT_EQUAL';
    const CONDITION_DIFFERENT = 'DIFFERENT';
    const CONDITION_IN = 'IN';
    const CONDITION_NOT_IN = 'NOT_IN';
    const CONDITION_IS_NULL = 'IS_NULL';
    const CONDITION_IS_NOT_NULL = 'IS_NOT_NULL';
    const CONDITION_GREATER_THAN = 'GREATER_THAN';
    const CONDITION_GREATER_EQUAL_THAN = 'GREATER_EQUAL_THAN';
    const CONDITION_LESS_THAN = 'LESS_THAN';
    const CONDITION_LESS_EQUAL_THAN = 'LESS_EQUAL_THAN';
    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';
    /**
     * Agrega filtros a un query doctrine
     * 
     * Ejemplo de formato de filtros
     * $filter = [
     *       [
     *           "groupLogic" => 'AND',
     *           "conditionsLogic" => 'AND',
     *           "conditions" => [
     *               [
     *                   "type" => 'like',
     *                   "value" => 'xxx',
     *                   "values" => ['xxx',"yyy"],
     *                   "property" => 'xxxx',
     *                   "not" => true, // solo algunos tipos @deprecated 1.0.7
     *                   "joinedAlias" => 'xxxx' // los joins se deben agregar previamente
     *               ]
     *           ]
     *       ]
     *   ];
     *
     * @param QueryBuilder $qb
     * @param array $filter
     * @return QueryBuilder
     */
    public static function addFilters(QueryBuilder $qb, array $filter): QueryBuilder
    {
        $qbCopy = clone $qb;
        if (empty($filter)) {
            return $qbCopy;
        }

        $alias = $qbCopy->getRootAliases()[0];
        $groups = $filter;

        if (empty($groups)) {
            return $qbCopy;
        }
        $conditionsGroup = array_map(function ($group) use ($alias, $qbCopy) {
            $condition = static::createGroupCondition($qbCopy, $group, $alias);
            return ["group" => $group, "condition" => $condition];
        }, $groups);

        $conditionGroupBase = array_shift($conditionsGroup);
        $conditionBase = $conditionGroupBase["condition"];
        foreach ($conditionsGroup as $groupCondition) {
            // no aplica la logica del primer grupo los demas se van agregando conforme a la logica del grupo que se va a agregar
            // Las condiciones se van combinando para formar (Todas las condiciones anteriores OR|AND Condición por agregar)
            $logic = $groupCondition["group"]["groupLogic"] ?? static::LOGIC_AND;
            $conditionBase = static::combineConditions($qbCopy, $conditionBase, $groupCondition["condition"], $logic);
        }

        $qbCopy->andWhere($conditionBase);
        return $qbCopy;
    }

    protected static function createGroupCondition(QueryBuilder $qb, array $group, string $alias)
    {

        $conditionLogic = $group["conditionsLogic"] ?? static::LOGIC_AND;
        $conditions = $group["conditions"];
        if (empty($conditions)) {
            return "";
        }
        $conditionBase = array_shift($conditions);
        static::addConditionParameter($qb, $alias, $conditionBase);
        $conditionBaseQuery = static::createCondition($alias, $conditionBase, $qb);
        foreach ($conditions as $condition) {
            $conditionQuery = static::createCondition($alias, $condition, $qb);
            $conditionBaseQuery = static::combineConditions($qb, $conditionBaseQuery, $conditionQuery, $conditionLogic);
            static::addConditionParameter($qb, $alias, $condition);
        }
        return $conditionBaseQuery;
    }

    protected static function combineConditions(QueryBuilder $qb,  $conditionBase, $condition, $logic)
    {
        $updatedCondition = $conditionBase;
        if ($logic === static::LOGIC_OR) {
            $updatedCondition = $qb->expr()->orX($conditionBase, $condition);
        } else {
            $updatedCondition = $qb->expr()->andX($conditionBase, $condition);
        }
        return $updatedCondition;
    }

    protected static function addConditionParameter(QueryBuilder $qb, $alias, $condition)
    {
        $errorMsg = 'La condición no tiene un valor adecuado';
        $values = $condition["values"] ?? null;
        $value = $condition["value"] ?? null;
        if ($condition["type"] === static::CONDITION_BETWEEN) {
            if (empty($values) || !is_array($values) || count($values) !== 2) {
                throw new Exception($errorMsg);
            }
            $parameters = static::getParameterKeyBetween($alias, $condition);
            $qb->setParameter($parameters[0], $values[0]);
            $qb->setParameter($parameters[1], $values[1]);
        } elseif ($condition["type"] === static::CONDITION_IN || $condition["type"] === static::CONDITION_NOT_IN) {
            if (empty($values) || !is_array($values)) {
                throw new Exception($errorMsg);
            }
            $parameter = static::getParameterKey($alias, $condition);
            $qb->setParameter($parameter, $values);
        } elseif ($condition["type"] !== static::CONDITION_IS_NULL && $condition !== static::CONDITION_IS_NOT_NULL) {
            if (!isset($value) || $value === null ||  (is_string($value) && trim($value) === "")) {
                throw new Exception($errorMsg);
            }
            $parameter = static::getParameterKey($alias, $condition);
            $qb->setParameter($parameter, $value);
        }
    }

    protected static function createCondition(string $rootAlias, array $condition, QueryBuilder $qb): string
    {
        $alias = static::calculateAlias($rootAlias, $condition);
        $type = $condition["type"];
        if ($type === static::CONDITION_LIKE) {
            $not = $condition["not"] ?? false;
            return static::createConditionLike($alias, $condition, $not);
        }
        if ($type === static::CONDITION_NOT_LIKE) {
            $not = true;
            return static::createConditionLike($alias, $condition, $not);
        }
        if ($type === static::CONDITION_EQUAL) {
            $not = $condition["not"] ?? false;
            return static::createConditionEqual($alias, $condition, $not);
        }
        if ($type === static::CONDITION_NOT_EQUAL) {
            $not = true;
            return static::createConditionEqual($alias, $condition, $not);
        }
        if ($type === static::CONDITION_DIFFERENT) {

            return static::createConditioDifferentThan($alias, $condition);
        }
        if ($type === static::CONDITION_BETWEEN) {
            return static::createConditionBetween($alias, $condition);
        }
        if ($type === static::CONDITION_IN) {
            $not = $condition["not"] ?? false;
            return static::createConditionIn($alias, $condition, $not, $qb);
        }
        if ($type === static::CONDITION_NOT_IN) {
            $not = true;
            return static::createConditionIn($alias, $condition, $not, $qb);
        }
        if ($type === static::CONDITION_IS_NULL) {
            $not = $condition["not"] ?? false;
            return static::createConditionIsNull($alias, $condition, $not, $qb);
        }
        if ($type === static::CONDITION_IS_NOT_NULL) {
            $not = true;
            return static::createConditionIsNull($alias, $condition, $not, $qb);
        }
        if ($type === static::CONDITION_GREATER_THAN) {
            return static::createConditioGreaterThan($alias, $condition);
        }
        if ($type === static::CONDITION_GREATER_EQUAL_THAN) {
            return static::createConditioGreaterEqualThan($alias, $condition);
        }
        if ($type === static::CONDITION_LESS_THAN) {
            return static::createConditioLessThan($alias, $condition);
        }
        if ($type === static::CONDITION_LESS_EQUAL_THAN) {
            return static::createConditioLessEqualThan($alias, $condition);
        }
        throw new Exception('El tipo de condicion no existe');
    }

    protected static function createConditionLike(string $alias, array $condition, bool $not): string
    {
        $column = $condition["property"];
        $parameter = static::getParameterKey($alias, $condition);
        if ($not) {
            return sprintf("%s.%s not like %s", $alias, $column, $parameter);
        } else {
            return sprintf("%s.%s like %s", $alias, $column, $parameter);
        }
    }
    protected static function createConditionIn(string $alias, array $condition, bool $not, QueryBuilder $qb): string
    {
        $column = $condition["property"];

        $parameter = static::getParameterKey($alias, $condition);
        if ($not) {
            $property = sprintf("%s.%s", $alias, $column);
            return $qb->expr()->notIn($property, $parameter);
        } else {
            $property = sprintf("%s.%s", $alias, $column);
            return $qb->expr()->in($property, $parameter);
        }
    }
    protected static function createConditionIsNull(string $alias, array $condition, bool $not, QueryBuilder $qb): string
    {
        $column = $condition["property"];

        if ($not) {
            $property = sprintf("%s.%s", $alias, $column);
            return $qb->expr()->isNull($property);
        } else {
            $property = sprintf("%s.%s", $alias, $column);
            return $qb->expr()->isNotNull($property);
        }
    }
    protected static function createConditionBetween(string $alias, array $condition): string
    {
        $property = $condition["property"];
        $value = $condition["value"];
        if (!is_array($value) || count($value) !== 2) {
            throw new Exception('Formato incorrecto el valor debe ser un array de strings para condición Between');
        }
        $parameters = static::getParameterKeyBetween($alias, $condition);
        return sprintf("%s.%s BETWEEN %s and %s", $alias, $property, $parameters[0], $parameters[1]);
    }
    protected static function createConditionEqual(string $alias, array $condition, bool $not): string
    {
        $column = $condition["property"];

        $parameter = static::getParameterKey($alias, $condition);
        if ($not) {
            return sprintf("%s.%s != %s", $alias, $column, $parameter);
        } else {
            return sprintf("%s.%s = %s", $alias, $column, $parameter);
        }
    }
    protected static function createConditioGreaterThan(string $alias, array $condition): string
    {
        $column = $condition["property"];
        $parameter = static::getParameterKey($alias, $condition);
        return sprintf("%s.%s > %s", $alias, $column, $parameter);
    }
    protected static function createConditioGreaterEqualThan(string $alias, array $condition): string
    {
        $column = $condition["property"];
        $parameter = static::getParameterKey($alias, $condition);
        return sprintf("%s.%s >= %s", $alias, $column, $parameter);
    }
    protected static function createConditioLessThan(string $alias, array $condition): string
    {
        $column = $condition["property"];
        $parameter = static::getParameterKey($alias, $condition);
        return sprintf("%s.%s < %s", $alias, $column, $parameter);
    }
    protected static function createConditioLessEqualThan(string $alias, array $condition): string
    {
        $column = $condition["property"];
        $parameter = static::getParameterKey($alias, $condition);
        return sprintf("%s.%s <= %s", $alias, $column, $parameter);
    }
    protected static function createConditioDifferentThan(string $alias, array $condition): string
    {
        $column = $condition["property"];
        $parameter = static::getParameterKey($alias, $condition);
        return sprintf("%s.%s <> %s", $alias, $column, $parameter);
    }

    protected static function getParameterKey(string $rootAlias, array $condition, string $postfix = ''): string
    {
        $alias = static::calculateAlias($rootAlias, $condition);
        $property = $condition["property"];
        $key = sprintf(":filter_%s_%s%s", $alias, $property, $postfix);
        return $key;
    }

    protected static function calculateAlias(string $rootAlias, array $condition): string
    {
        $alias = (isset($condition["joinedAlias"]) && !empty($condition["joinedAlias"])) ?  $condition["joinedAlias"] : $rootAlias;
        return $alias;
    }
    protected static function getParameterKeyBetween(string $rootAlias, array $condition): array
    {
        $first = static::getParameterKey($rootAlias, $condition, "_fist");
        $second = static::getParameterKey($rootAlias, $condition, "_second");
        return [$first, $second];
    }
}

<?php

namespace PDSSUtilities;

use Exception;
use Doctrine\ORM\QueryBuilder;

/**
 * Utilidades para agregar joins en un Query dependiendo de los parámetros de la solicitud http
 */
class QueryJoins
{

    const LEFT_JOIN = 'LEFT'; // valor predeterminado
    const INNER_JOIN = 'INNER';
    /**
     * Agrega joins en el orden en que se encuentran a un query doctrine
     * 
     * Ejemplo de formato de join
     * $joins = [
     *  [
     *      "joinType": 'LEFT_JOIN|INNER_JOIN|RIGHT_JOIN'
     *      "alias": 'xxxx',
     *      "property": 'xxxx', 
     *      "joinedProperty" => 'xxxx' // los joins se deben agregar previamente (alias de un join agregado previamente)
     *  ]
     * ]
     *
     * @param QueryBuilder $qb
     * @param array $joins
     * @return QueryBuilder
     */
    public static function addJoins(QueryBuilder $qb, array $joins): QueryBuilder
    {
        $qbCopy = clone $qb;
        if (empty($joins)) {
            return $qbCopy;
        }
        $rootAlias = $qbCopy->getRootAliases()[0];
        foreach($joins as $join) {
            static::addJoin($qbCopy, $join, $rootAlias);
        }
        return $qbCopy;
    }

    private static function addJoin(QueryBuilder $qb, array $item, $rootAlias) {
        $alias =   static::calculateAlias($rootAlias, $item);
        $type = $item["joinType"] ?? static::LEFT_JOIN;
        $property = $item["property"];
        $propertyAlias = $item["alias"] ?? $property;
        if ($type === static::INNER_JOIN) {
            $qb->innerJoin("{$alias}.{$property}", $propertyAlias);
        } else {
            $qb->leftJoin("{$alias}.{$property}", $propertyAlias);
        }
    }
    protected static function calculateAlias(string $rootAlias, array $item): string {
        $alias = (isset($item["joinedProperty"]) && !empty($item["joinedProperty"])) ?  $item["joinedProperty"] :$rootAlias ;
        return $alias;
    }

}

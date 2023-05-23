<?php

declare(strict_types=1);

namespace AppTest\Integration;

use AppEntities\User;
use AppLibrary\EntityManagerFactory;
use PDSSUtilities\QueryFilter;
use PHPUnit\Framework\TestCase;

final class QueryFilterTest extends TestCase

{
    private $filterBase;
    protected function setUp(): void
    {
        $this->filterBase = [
            [
                "groupLogic" => 'AND',
                "conditionsLogic" => 'AND',
                "conditions" => [
                    [
                        "type" => 'GREATER_THAN',
                        "value" => '5',
                        "property" => 'id',
                    ]
                ]
            ]
        ];
    }
    public function testGreaterThan()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_GREATER_THAN;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $total, "Test CONDITION_GREATER_THAN");
    }
    public function testGreaterThanAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_GREATER_THAN_ALIAS;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $total, "Test CONDITION_GREATER_THAN_ALIAS");
    }
    public function testGreaterEqualThan()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_GREATER_EQUAL_THAN;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(2, $total, "Test CONDITION_GREATER_EQUAL_THAN");
    }
    public function testGreaterEqualThanAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_GREATER_EQUAL_THAN_ALIAS;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(2, $total, "Test CONDITION_GREATER_EQUAL_THAN_ALIAS");
    }
    public function testLessThan()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_LESS_THAN;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(4, $total, "Test CONDITION_LESS_THAN");
    }
    public function testLessThanAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_LESS_THAN_ALIAS;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(4, $total, "Test CONDITION_LESS_THAN_ALIAS");
    }
    public function testLessEqualThan()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_LESS_EQUAL_THAN;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_LESS_THAN");
    }
    public function testLessEqualThanAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_LESS_EQUAL_THAN_ALIAS;
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_LESS_EQUAL_THAN_ALIAS");
    }
    public function testLike()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_LIKE;
        $filter[0]["conditions"][0]["value"] = '%Cespedes';
        $filter[0]["conditions"][0]["property"] = 'name';
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $total, "Test CONDITION_LIKE");
    }
    public function testNotLike()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_NOT_LIKE;
        $filter[0]["conditions"][0]["value"] = '%Cespedes';
        $filter[0]["conditions"][0]["property"] = 'name';
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_NOT_LIKE");
    }
    public function testBetween()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_BETWEEN;
        $filter[0]["conditions"][0]["values"] = ["1", "3"];
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(3, $total, "Test CONDITION_BETWEEN");
    }
    public function testEqual()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_EQUAL;
        $filter[0]["conditions"][0]["value"] = "1";
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $total, "Test CONDITION_EQUAL");
    }
    public function testEqualAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_EQUAL_ALIAS;
        $filter[0]["conditions"][0]["value"] = "1";
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $total, "Test CONDITION_EQUAL_ALIAS");
    }
    public function testNotEqual()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_NOT_EQUAL;
        $filter[0]["conditions"][0]["value"] = "1";
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_EQUAL_ALIAS");
    }
    public function testNotEqualAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_NOT_EQUAL;
        $filter[0]["conditions"][0]["value"] = "1";
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_EQUAL_ALIAS");
    }
    public function testDifferent()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_DIFFERENT;
        $filter[0]["conditions"][0]["value"] = "1";
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_DIFFERENT");
    }
    public function testDifferentAlias()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_DIFFERENT_ALIAS;
        $filter[0]["conditions"][0]["value"] = "1";
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(5, $total, "Test CONDITION_DIFFERENT_ALIAS");
    }
    public function testIn()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_IN;
        $filter[0]["conditions"][0]["values"] = ["1", "3"];
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(2, $total, "Test CONDITION_IN");
    }
    public function testNotIn()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_NOT_IN;
        $filter[0]["conditions"][0]["values"] = ["1", "3"];
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(4, $total, "Test CONDITION_IN");
    }
    public function testIsNull()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_IS_NULL;
        $filter[0]["conditions"][0]["property"] = 'role';
        unset($filter[0]["conditions"][0]["values"], $filter[0]["conditions"][0]["value"]);
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(4, $total, "Test CONDITION_IS_NULL");
    }
    public function testIsNotNull()
    {
        $filter = $this->filterBase;
        $filter[0]["conditions"][0]["type"] = QueryFilter::CONDITION_IS_NOT_NULL;
        $filter[0]["conditions"][0]["property"] = 'role';
        unset($filter[0]["conditions"][0]["values"], $filter[0]["conditions"][0]["value"]);
        $entityManager = EntityManagerFactory::getInstance();
        $qb = $entityManager->createQueryBuilder()->from(User::class, 'user')->select("COUNT(user.id)");
        $qb = QueryFilter::addFilters($qb, $filter);
        $total = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(2, $total, "Test CONDITION_IS_NOT_NULL");
    }
}

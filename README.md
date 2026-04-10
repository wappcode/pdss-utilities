# PDSS-Utilities

Utilidades PHP para trabajar con Doctrine ORM.

## Requisitos

- PHP >= 8.0
- Doctrine ORM >= 3.0

## Instalación

Instalar la librería usando Composer:

```bash
composer require wappcode/pdss-utilities
```

### Instalar dependencia de Doctrine ORM

Si aún no tienes Doctrine ORM instalado:

```bash
composer require doctrine/orm
```

---

## Clases Base para Entidades

Clases abstractas que proveen gestión automática de timestamps (`created` y `updated`) y diferentes tipos de identificadores.

### AbstractEntityModel

Clase base para entidades con ID tipo **integer auto-generado**.

```php
use PDSSUtilities\AbstractEntityModel;

#[ORM\Entity]
class Product extends AbstractEntityModel
{
    #[ORM\Column(type: 'string')]
    private string $name;
    
    // Los campos id, created y updated están heredados
}
```

**Propiedades heredadas:**
- `id`: `?int` - Auto-generado por Doctrine
- `created`: `DateTimeImmutable` - Timestamp de creación
- `updated`: `DateTimeImmutable` - Timestamp de última actualización

### AbstractEntityModelUlid

Clase base para entidades con ID tipo **ULID** (26 caracteres).

```php
use PDSSUtilities\AbstractEntityModelUlid;

#[ORM\Entity]
class User extends AbstractEntityModelUlid
{
    #[ORM\Column(type: 'string')]
    private string $email;
}
```

**Propiedades heredadas:**
- `id`: `?string` - ULID de 26 caracteres (sortable por timestamp)
- `created`: `DateTimeImmutable`
- `updated`: `DateTimeImmutable`

### AbstractEntityModelKsuid

Clase base para entidades con ID tipo **KSUID** (27 caracteres).

```php
use PDSSUtilities\AbstractEntityModelKsuid;

#[ORM\Entity]
class Order extends AbstractEntityModelKsuid
{
    #[ORM\Column(type: 'decimal')]
    private float $total;
}
```

**Propiedades heredadas:**
- `id`: `?string` - KSUID de 27 caracteres (K-Sortable Unique Identifier)
- `created`: `DateTimeImmutable`
- `updated`: `DateTimeImmutable`

### AbstractEntityModelUuidV4

Clase base para entidades con ID tipo **UUID v4** (36 caracteres).

```php
use PDSSUtilities\AbstractEntityModelUuidV4;

#[ORM\Entity]
class Invoice extends AbstractEntityModelUuidV4
{
    #[ORM\Column(type: 'string')]
    private string $number;
}
```

**Propiedades heredadas:**
- `id`: `?string` - UUID v4 de 36 caracteres (formato: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx)
- `created`: `DateTimeImmutable`
- `updated`: `DateTimeImmutable`

### Métodos Disponibles

Todas las clases abstract proveen:

```php
public function getId(): ?string|?int;
public function getCreated(): DateTimeImmutable;
public function getUpdated(): DateTimeImmutable;
public function __toString(): string;
protected function setUpdated(): self; // Para actualización manual
```

---

## ¿Qué tipo de ID elegir?

### AbstractEntityModel (Integer)

**Cuándo usar:**
- Tablas pequeñas a medianas (< 2 mil millones de registros)
- Sistemas legacy que requieren IDs numéricos
- Cuando el orden de inserción es importante y secuencial
- APIs públicas donde prefieres IDs cortos y simples
- Cuando necesitas operaciones matemáticas con IDs

**Ventajas:**
- ✅ Tamaño mínimo (4-8 bytes)
- ✅ Índices más rápidos y compactos
- ✅ Fácil de leer y debuggear
- ✅ Compatible con sistemas antiguos

**Desventajas:**
- ❌ Predecible (riesgo de seguridad en APIs públicas)
- ❌ Revela cantidad de registros
- ❌ Problemas en sistemas distribuidos
- ❌ Límite de ~4 mil millones (INT) o ~9 quintillones (BIGINT)

### AbstractEntityModelUlid (26 caracteres)

**Cuándo usar:**
- **Recomendado para la mayoría de casos**
- Sistemas modernos que requieren IDs únicos globalmente
- Cuando necesitas ordenamiento cronológico natural
- Microservicios y arquitecturas distribuidas
- Migración desde sistemas con auto-increment

**Ventajas:**
- ✅ Sortable por tiempo de creación
- ✅ Único globalmente sin coordinación
- ✅ Más compacto que UUID (26 vs 36 caracteres)
- ✅ Case-insensitive (Base32)
- ✅ Legible (no usa caracteres ambiguos)
- ✅ Performance excelente

**Desventajas:**
- ❌ Más grande que integers (26 bytes vs 4-8)
- ❌ No es estándar universal como UUID

**Ideal para:** Users, Orders, Products, Posts, Comments

### AbstractEntityModelKsuid (27 caracteres)

**Cuándo usar:**
- Similar a ULID pero con mayor precisión temporal
- Sistemas que requieren ordenamiento muy preciso
- Cuando necesitas timestamp en epoch específico (2014)
- Requerimiento específico de formato KSUID

**Ventajas:**
- ✅ Sortable por tiempo (epoch 2014)
- ✅ Único globalmente
- ✅ Buena distribución en índices

**Desventajas:**
- ❌ Case-sensitive (Base62: A-Z, a-z, 0-9)
- ❌ Menos común que ULID o UUID
- ❌ Requiere extensión GMP
- ❌ 27 caracteres (el más largo)

**Ideal para:** Logs, Events, Audit trails, Time-series data

### AbstractEntityModelUuidV4 (36 caracteres)

**Cuándo usar:**
- Interoperabilidad con sistemas externos que requieren UUID
- Estándares específicos de tu industria
- Cuando la aleatoriedad completa es crítica
- Integración con APIs que esperan UUID
- Sistemas que ya usan UUID y no quieres migrar

**Ventajas:**
- ✅ Estándar RFC 4122 (universal)
- ✅ Ampliamente soportado
- ✅ Completamente aleatorio (seguridad)
- ✅ Compatible con bases de datos nativas UUID

**Desventajas:**
- ❌ No sortable (aleatoriedad completa)
- ❌ El más largo (36 caracteres con guiones)
- ❌ Peor performance en índices (fragmentación)
- ❌ Menos legible

**Ideal para:** Session IDs, API Keys, Integration tokens, External references

### Comparación Rápida

| Tipo | Tamaño | Sortable | Velocidad | Uso Recomendado |
|------|--------|----------|-----------|-----------------|
| **Integer** | 4-8 bytes | ✅ Secuencial | ⚡⚡⚡ Muy rápido | Tablas pequeñas, legacy |
| **ULID** | 26 chars | ✅ Por tiempo | ⚡⚡ Rápido | **Uso general (recomendado)** |
| **KSUID** | 27 chars | ✅ Por tiempo | ⚡⚡ Rápido | Time-series, logs |
| **UUID v4** | 36 chars | ❌ Aleatorio | ⚡ Moderado | Interoperabilidad, APIs externas |

---

## Sobreescribir Columnas Heredadas

Puedes personalizar las columnas heredadas en tu entidad usando el atributo `#[ORM\AttributeOverride]`:

### Cambiar nombre de columna

```php
use PDSSUtilities\AbstractEntityModelUlid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\AttributeOverride(
    name: 'id',
    column: new ORM\Column(name: 'product_id', type: 'string', length: 26)
)]
class Product extends AbstractEntityModelUlid
{
    // La columna 'id' ahora se llama 'product_id' en la base de datos
}
```

### Cambiar longitud del ID

```php
#[ORM\Entity]
#[ORM\AttributeOverride(
    name: 'id',
    column: new ORM\Column(name: 'id', type: 'string', length: 50)
)]
class CustomEntity extends AbstractEntityModelUlid
{
    // El ID ahora permite hasta 50 caracteres
}
```

### Cambiar nombre de columnas de timestamps

```php
#[ORM\Entity]
#[ORM\AttributeOverrides([
    new ORM\AttributeOverride(
        name: 'created',
        column: new ORM\Column(name: 'created_at', type: 'datetimetz_immutable')
    ),
    new ORM\AttributeOverride(
        name: 'updated',
        column: new ORM\Column(name: 'updated_at', type: 'datetimetz_immutable')
    )
])]
class Article extends AbstractEntityModelUlid
{
    // Las columnas ahora se llaman 'created_at' y 'updated_at'
}
```

### Cambiar tipo de columna ID (integer)

```php
#[ORM\Entity]
#[ORM\AttributeOverride(
    name: 'id',
    column: new ORM\Column(name: 'id', type: 'bigint')
)]
class LargeTable extends AbstractEntityModel
{
    // El ID ahora es BIGINT en lugar de INTEGER
}
```

---

## Utilidades de Query

### QueryFilter

Aplica filtros dinámicos a un QueryBuilder de Doctrine basado en parámetros HTTP.

**Operadores disponibles:**
- `EQUAL` / `=` - Igualdad
- `NOT_EQUAL` / `!=` - Diferente
- `DIFFERENT` / `<>` - Diferente
- `GREATER_THAN` / `>` - Mayor que
- `LESS_THAN` / `<` - Menor que
- `GREATER_EQUAL_THAN` / `>=` - Mayor o igual
- `LESS_EQUAL_THAN` / `<=` - Menor o igual
- `LIKE` - Búsqueda con comodín
- `NOT_LIKE` - Búsqueda con comodín negada
- `IN` - En lista de valores
- `NOT_IN` - No en lista de valores
- `BETWEEN` - Entre dos valores
- `IS_NULL` - Es nulo
- `IS_NOT_NULL` - No es nulo

**Ejemplo:**

```php
use PDSSUtilities\QueryFilter;

$filter = [
    [
        "groupLogic" => "AND",
        "conditionsLogic" => "AND",
        "conditions" => [
            [
                "filterOperator" => "like",
                "value" => ["single" => "John"],
                "property" => "name"
            ],
            [
                "filterOperator" => ">=",
                "value" => ["single" => 18],
                "property" => "age"
            ]
        ]
    ]
];

$qb = $entityManager->createQueryBuilder()
    ->select('u')
    ->from(User::class, 'u');

$qb = QueryFilter::addFilters($qb, $filter);
```

**Filtros con joins:**

```php
$filter = [
    [
        "groupLogic" => "AND",
        "conditionsLogic" => "OR",
        "conditions" => [
            [
                "filterOperator" => "=",
                "value" => ["single" => "active"],
                "property" => "status",
                "onJoinedProperty" => "profile"  // Aplicar filtro en tabla relacionada
            ]
        ]
    ]
];
```

**Filtros compuestos:**

```php
$filter = [
    [
        "groupLogic" => "AND",
        "conditionsLogic" => "AND",
        "conditions" => [
            [
                "filterOperator" => "=",
                "value" => ["single" => "admin"],
                "property" => "role"
            ]
        ],
        "compoundConditions" => [
            [
                "conditionsLogic" => "OR",
                "conditions" => [
                    [
                        "filterOperator" => "like",
                        "value" => ["single" => "%@example.com"],
                        "property" => "email"
                    ],
                    [
                        "filterOperator" => "like",
                        "value" => ["single" => "%@test.com"],
                        "property" => "email"
                    ]
                ],
                "compoundConditions" => []
            ]
        ]
    ]
];
```

### QueryJoins

Agrega joins dinámicos a un QueryBuilder.

**Tipos de join:**
- `LEFT` - LEFT JOIN (predeterminado)
- `INNER` - INNER JOIN

**Ejemplo:**

```php
use PDSSUtilities\QueryJoins;

$joins = [
    [
        "joinType" => "LEFT",
        "alias" => "profile",
        "property" => "profile"
    ],
    [
        "joinType" => "INNER",
        "alias" => "orders",
        "property" => "orders"
    ],
    [
        "joinType" => "LEFT",
        "alias" => "items",
        "property" => "items",
        "joinedProperty" => "orders"  // Join desde otro join
    ]
];

$qb = $entityManager->createQueryBuilder()
    ->select('u')
    ->from(User::class, 'u');

$qb = QueryJoins::addJoins($qb, $joins);
// Resultado: FROM User u LEFT JOIN u.profile profile INNER JOIN u.orders orders LEFT JOIN orders.items items
```

### QuerySelect

Calcula el valor SELECT para queries con selección parcial de propiedades.

**Ejemplo:**

```php
use PDSSUtilities\QuerySelect;

$select = [
    'properties' => ['id', 'name', 'email'],  // Propiedades de la entidad raíz
    'joins' => [
        [
            'joinedAlias' => 'profile',
            'properties' => ['id', 'bio', 'avatar']
        ],
        [
            'joinedAlias' => 'orders',
            'properties' => ['id', 'total', 'status']
        ]
    ]
];

$selectValues = QuerySelect::createDoctrineSelectValue('u', $select);
// Resultado: ['partial u.{id,name,email}', 'partial profile.{id,bio,avatar}', 'partial orders.{id,total,status}']

$qb->select($selectValues);
```

**Sin propiedades específicas:**

```php
$select = [
    'properties' => [],  // Selecciona todo de la raíz
    'joins' => [
        [
            'joinedAlias' => 'profile',
            'properties' => []  // Selecciona todo del join
        ]
    ]
];

$selectValues = QuerySelect::createDoctrineSelectValue('u', $select);
// Resultado: ['u', 'profile']
```

### QuerySort

Aplica ordenamiento dinámico a un QueryBuilder.

**Direcciones:**
- `asc` - Ascendente
- `desc` - Descendente

**Ejemplo:**

```php
use PDSSUtilities\QuerySort;

$orderBy = [
    [
        "direction" => "desc",
        "property" => "created"
    ],
    [
        "direction" => "asc",
        "property" => "name"
    ]
];

$qb = $entityManager->createQueryBuilder()
    ->select('u')
    ->from(User::class, 'u');

$qb = QuerySort::addOrderBy($qb, $orderBy);
// Resultado: ORDER BY u.created DESC, u.name ASC
```

**Ordenar por propiedades de joins:**

```php
$orderBy = [
    [
        "direction" => "desc",
        "property" => "createdAt",
        "onJoinedProperty" => "orders"  // Ordenar por campo de tabla relacionada
    ]
];
```

**Desde string JSON:**

```php
$orderByJson = '[{"direction":"desc","property":"created"}]';
$orderBy = QuerySort::standardizeRequestParams($orderByJson);
$qb = QuerySort::addOrderBy($qb, $orderBy);
```

---

## Seguridad en Queries Dinámicas

### Datos desde HTTP POST/GET

Las utilidades `QueryFilter`, `QueryJoins`, `QuerySelect` y `QuerySort` están diseñadas para recibir datos desde peticiones HTTP (POST, GET, etc.). Es **fundamental** aplicar las validaciones de seguridad adecuadas.

**Ejemplo de uso con datos POST:**

```php
// Controlador recibiendo datos POST
$requestData = json_decode(file_get_contents('php://input'), true);

$filters = $requestData['filters'] ?? [];
$joins = $requestData['joins'] ?? [];
$select = $requestData['select'] ?? [];
$orderBy = $requestData['orderBy'] ?? [];

// Aplicar a QueryBuilder
$qb = $entityManager->createQueryBuilder()
    ->select('u')
    ->from(User::class, 'u');

$qb = QueryJoins::addJoins($qb, $joins);
$qb = QueryFilter::addFilters($qb, $filters);
$qb = QuerySort::addOrderBy($qb, $orderBy);
```

### Protección de Doctrine contra SQL Injection

Doctrine ORM aplica **prepared statements y parameter binding automáticamente**, lo que protege contra inyección SQL:

```php
// Doctrine convierte esto:
$qb->where('u.name = :name')->setParameter('name', $userInput);

// En un prepared statement:
// SELECT * FROM users WHERE name = ? 
// Binding: ['John']
```

**✅ Seguro por defecto:**
- Todos los valores en `QueryFilter` se pasan como parámetros vinculados
- Los operadores están validados contra constantes de la clase
- Las propiedades se concatenan como identificadores, no valores

### Validaciones de Seguridad Recomendadas

#### 1. **Whitelist de propiedades permitidas**

```php
class QueryValidator
{
    private const ALLOWED_PROPERTIES = [
        'User' => ['id', 'name', 'email', 'created', 'updated'],
        'Profile' => ['id', 'bio', 'avatar'],
        'Order' => ['id', 'total', 'status', 'created']
    ];
    
    public static function validateProperty(string $entity, string $property): bool
    {
        return in_array($property, self::ALLOWED_PROPERTIES[$entity] ?? [], true);
    }
    
    public static function validateFilters(array $filters, string $entity): void
    {
        foreach ($filters as $group) {
            foreach ($group['conditions'] ?? [] as $condition) {
                if (!self::validateProperty($entity, $condition['property'])) {
                    throw new \InvalidArgumentException("Propiedad no permitida: {$condition['property']}");
                }
            }
        }
    }
}

// Uso:
try {
    QueryValidator::validateFilters($filters, 'User');
    $qb = QueryFilter::addFilters($qb, $filters);
} catch (\InvalidArgumentException $e) {
    // Manejar error de validación
}
```

#### 2. **Whitelist de joins permitidos**

```php
class QueryValidator
{
    private const ALLOWED_JOINS = [
        'User' => ['profile', 'orders', 'roles'],
        'Order' => ['items', 'user'],
    ];
    
    public static function validateJoins(array $joins, string $entity): void
    {
        foreach ($joins as $join) {
            $property = $join['property'];
            if (!in_array($property, self::ALLOWED_JOINS[$entity] ?? [], true)) {
                throw new \InvalidArgumentException("Join no permitido: {$property}");
            }
        }
    }
}
```

#### 3. **Limitar operadores permitidos**

```php
class QueryValidator
{
    private const ALLOWED_OPERATORS = [
        'public' => ['=', 'like', '>', '<', '>=', '<=', 'in'],
        'admin' => ['=', '!=', 'like', 'not_like', '>', '<', '>=', '<=', 'in', 'not_in', 'between', 'is_null', 'is_not_null']
    ];
    
    public static function validateOperator(string $operator, string $userRole): bool
    {
        $allowed = self::ALLOWED_OPERATORS[$userRole] ?? self::ALLOWED_OPERATORS['public'];
        return in_array(strtolower($operator), $allowed, true);
    }
}
```

#### 4. **Validar tipos de datos**

```php
class QueryValidator
{
    public static function validateFilterValue(array $condition): void
    {
        $property = $condition['property'];
        $value = $condition['value']['single'] ?? $condition['value']['many'] ?? null;
        
        // Ejemplo: validar que 'age' sea numérico
        if ($property === 'age' && !is_numeric($value)) {
            throw new \InvalidArgumentException("El valor de 'age' debe ser numérico");
        }
        
        // Ejemplo: validar formato de email
        if ($property === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Formato de email inválido");
        }
    }
}
```

#### 5. **Limitar profundidad de queries**

```php
class QueryValidator
{
    private const MAX_JOIN_DEPTH = 3;
    private const MAX_FILTER_GROUPS = 5;
    private const MAX_CONDITIONS_PER_GROUP = 10;
    
    public static function validateComplexity(array $filters, array $joins): void
    {
        if (count($joins) > self::MAX_JOIN_DEPTH) {
            throw new \InvalidArgumentException("Demasiados joins");
        }
        
        if (count($filters) > self::MAX_FILTER_GROUPS) {
            throw new \InvalidArgumentException("Demasiados grupos de filtros");
        }
        
        foreach ($filters as $group) {
            if (count($group['conditions'] ?? []) > self::MAX_CONDITIONS_PER_GROUP) {
                throw new \InvalidArgumentException("Demasiadas condiciones por grupo");
            }
        }
    }
}
```

### Ejemplo Completo con Seguridad

```php
// Controlador con validación completa
class UserController
{
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        // 1. Obtener datos del request
        $filters = $request->request->get('filters', []);
        $joins = $request->request->get('joins', []);
        $orderBy = $request->request->get('orderBy', []);
        $select = $request->request->get('select', []);
        
        try {
            // 2. Validar datos de entrada
            QueryValidator::validateComplexity($filters, $joins);
            QueryValidator::validateFilters($filters, 'User');
            QueryValidator::validateJoins($joins, 'User');
            
            // 3. Construir query
            $qb = $em->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u');
            
            $qb = QueryJoins::addJoins($qb, $joins);
            $qb = QueryFilter::addFilters($qb, $filters);
            $qb = QuerySort::addOrderBy($qb, $orderBy);
            
            // 4. Aplicar límites
            $qb->setMaxResults(100); // Limitar resultados
            
            // 5. Ejecutar
            $results = $qb->getQuery()->getResult();
            
            return new JsonResponse($results);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
```

### Checklist de Seguridad

- ✅ **Validar propiedades** contra whitelist
- ✅ **Validar joins** contra relaciones permitidas
- ✅ **Validar operadores** según rol de usuario
- ✅ **Validar tipos** de valores (numéricos, emails, fechas)
- ✅ **Limitar complejidad** (joins, filtros, condiciones)
- ✅ **Aplicar límites** con `setMaxResults()`
- ✅ **Usar permisos** basados en roles
- ✅ **Log de queries** sospechosas para auditoría
- ✅ **Rate limiting** en endpoints de búsqueda
- ✅ **Doctrine se encarga** del parameter binding (SQL injection)

---

## Ejemplo Completo

```php
use PDSSUtilities\AbstractEntityModelUlid;
use PDSSUtilities\QueryFilter;
use PDSSUtilities\QueryJoins;
use PDSSUtilities\QuerySelect;
use PDSSUtilities\QuerySort;

// 1. Definir entidad
#[ORM\Entity]
class User extends AbstractEntityModelUlid
{
    #[ORM\Column(type: 'string')]
    private string $name;
    
    #[ORM\OneToOne(targetEntity: Profile::class)]
    private Profile $profile;
    
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;
}

// 2. Construir query dinámica
$qb = $entityManager->createQueryBuilder()
    ->select('u')
    ->from(User::class, 'u');

// Agregar joins
$joins = [
    ["alias" => "profile", "property" => "profile"],
    ["alias" => "orders", "property" => "orders"]
];
$qb = QueryJoins::addJoins($qb, $joins);

// Agregar filtros
$filter = [
    [
        "groupLogic" => "AND",
        "conditionsLogic" => "AND",
        "conditions" => [
            ["filterOperator" => "like", "value" => ["single" => "%John%"], "property" => "name"]
        ]
    ]
];
$qb = QueryFilter::addFilters($qb, $filter);

// Agregar ordenamiento
$orderBy = [
    ["direction" => "desc", "property" => "created"]
];
$qb = QuerySort::addOrderBy($qb, $orderBy);

// Selección parcial
$select = [
    'properties' => ['id', 'name', 'created'],
    'joins' => [
        ['joinedAlias' => 'profile', 'properties' => ['id', 'bio']]
    ]
];
$selectValues = QuerySelect::createDoctrineSelectValue('u', $select);
$qb->select($selectValues);

// Ejecutar query
$results = $qb->getQuery()->getResult();
```
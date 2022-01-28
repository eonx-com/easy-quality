<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use PhpParser\Node\Name\FullyQualified;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\ValueObject\PhpVersionFeature;

/**
 * @see \EonX\EasyQuality\Tests\Rector\DateTimeImmutablePropertyTypeRector\DateTimeImmutablePropertyTypeRectorTest
 */
final class DateTimeImmutablePropertyTypeRector extends AbstractRector
{
    /**
     * @var string
     */
    public const REPLACE_PAIRS = 'replace_pairs';

    /**
     * @var string[][]
     */
    private array $replacePairs = [];

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->replacePairs = $configuration[self::REPLACE_PAIRS] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @noinspection AutoloadingIssuesInspection
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fix property type from Carbon, Datetime to CarbonImmutable, DatetimeImmutable',
            [
                new ConfiguredCodeSample(
                    <<<'PHP'
final class SomeClass
{
    private DateTime $one;
    
    private ?Carbon $two;
}
PHP
                    ,
                    <<<'PHP'
final class SomeClass
{
    private DateTimeImmutable $one;
    
    private ?CarbonImmutable $two;
}
PHP
                    ,
                    [
                        self::REPLACE_PAIRS => [
                            'replaceFrom' => 'replaceTo',
                        ],
                    ]
                ),
            ]
        );
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::TYPED_PROPERTIES;
    }

    /**
     * @param \PhpParser\Node\Stmt\Property $node
     */
    public function refactor(Node $node): ?Node
    {
        $scope = $node->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            return null;
        }

        $propertyName = $this->getPropertyName($node);
        if($this->shouldSkip($propertyName)){
            return null;
        }

        $typeNode = $this->createNewPropertyTypeNode($propertyName);

        if ($node->type instanceof Node\NullableType) {
            $node->type->type = $typeNode;
        } else {
            $node->type = $typeNode;
        }

        return $node;
    }

    /**
     * @param string $propertyName
     */
    private function createNewPropertyTypeNode(string $propertyName): Node
    {
        $typeNode = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode(
            new ObjectType((string) $this->replacePairs[$propertyName]),
            TypeKind::PROPERTY()
        );
        if ($typeNode === null) {
            // invalid configuration
            throw new ShouldNotHappenException();
        }

        return $typeNode;
    }

    private function shouldSkip(?string $propertyName): bool
    {
        $propertiesToReplace = \array_keys($this->replacePairs);
        if (\in_array($propertyName, $propertiesToReplace, true) === false) {
            return true;
        }

        return false;
    }

    /**
     * @param Property $node
     */
    private function getPropertyName(Property $node): ?string
    {
        $propertyType = $node->type;
        if ($propertyType === null) {
            return null;
        }
        if ($this->nodeTypeResolver->isNullableType($propertyType)) {
            $propertyType = $propertyType->type;
        }
        if ($propertyType instanceof FullyQualified === false) {
            return null;
        }
        return \implode('\\', $propertyType->parts);
    }
}

<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node\Identifier;

/**
 * @see \EonX\EasyQuality\Tests\Rector\DoctrineAttributeDateTimeImmutableRector\DoctrineAttributeDateTimeImmutableRectorTest
 */
final class DoctrineAttributeDateTimeImmutableRector extends AbstractRector
{
    /**
     * @var string
     */
    private const ATTRIBUTE_KEY = 'type';

    /**
     * @var string
     */
    public const REPLACE_TYPES = 'replace_types';

    /**
     * @var string
     */
    private const TYPE_COLUMN = 'Doctrine\\ORM\\Mapping\\Column';

    /**
     * @var string[][]
     */
    private array $replacePairs = [];

    public function getNodeTypes(): array
    {
        return [Attribute::class];
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->replacePairs = $configuration[self::REPLACE_TYPES] ?? [];
    }

    /**
     * @noinspection AutoloadingIssuesInspection
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fix doctrine attribute column type date/datetime to date_immutable/datetime_immutable',
            [
                new ConfiguredCodeSample(
                    <<<'PHP'
    #[ORM\Column(type: 'date')]
    #[ORM\Column(type: 'datetime')]
    PHP
                    ,
                    <<<'PHP'
    #[ORM\Column(type: 'date_immutable')]
    #[ORM\Column(type: 'datetime_immutable')]
    PHP
                    ,
                    [
                        self::REPLACE_TYPES => [
                            'replaceFrom' => 'replaceTo',
                        ],
                    ]
                )
            ]
        );
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, self::TYPE_COLUMN)) {
            return null;
        }
        foreach ($node->args as $arg) {
            $argName = $arg->name;
            if (!$argName instanceof Identifier) {
                continue;
            }
            if (!$this->isName($argName, self::ATTRIBUTE_KEY)) {
                continue;
            }
            /** @var string $value */
            $value = $this->valueResolver->getValue($arg->value);
            if (isset($this->replacePairs[$value])) {
                $arg->value = new String_((string) $this->replacePairs[$value], [
                    'kind' => $arg->value->getAttribute('kind'),
                ]);

                return $node;
            }
        }

        return null;
    }
}

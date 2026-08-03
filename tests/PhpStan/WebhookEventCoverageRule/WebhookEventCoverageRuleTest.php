<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule;

use EonX\EasyQuality\PhpStan\AssertedWebhookEventCollector;
use EonX\EasyQuality\PhpStan\WebhookEventCoverageRule;
use EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub\UnbackedWebhookEventStub;
use EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub\WebhookEventStub;
use EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub\WebhookStub;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\EonX\EasyQuality\PhpStan\WebhookEventCoverageRule>
 */
final class WebhookEventCoverageRuleTest extends RuleTestCase
{
    private const string FIXTURE = __DIR__ . '/Fixture/AssertsSomeEvents.php';

    /**
     * The events the fixture leaves uncovered, the enum stub declares them on lines 12, 14 and 16.
     *
     * @var string[]
     */
    private const array NOT_ASSERTED_EVENTS = [
        'not_asserted:plain',
        'not_asserted:other_entity',
        'not_asserted:negatively',
    ];

    private string $enumClass = WebhookEventStub::class;

    /**
     * @var string[]
     */
    private array $excludedEvents = [];

    /**
     * @see testRule
     */
    public static function provideData(): iterable
    {
        yield 'reports the webhook events that no test asserts' => [
            [],
            [
                [self::getNotAssertedError('NotAsserted', 'not_asserted:plain'), 12],
                [self::getNotAssertedError('NotAssertedForOtherEntity', 'not_asserted:other_entity'), 14],
                [self::getNotAssertedError('NotAssertedNegatively', 'not_asserted:negatively'), 16],
            ],
        ];

        yield 'reports the excluded event that is asserted or not a case of the enum' => [
            [
                WebhookEventStub::AssertedInChain->value,
                'unknown:event',
                ...self::NOT_ASSERTED_EVENTS,
            ],
            [
                [
                    'The excluded webhook event unknown:event is not a case of the event enum.'
                    . ' Remove it from the excluded events.',
                    1,
                ],
                [
                    'The excluded webhook event asserted:chain is asserted by a test.'
                    . ' Remove it from the excluded events.',
                    8,
                ],
            ],
        ];

        yield 'reports nothing when every not asserted event is excluded' => [
            self::NOT_ASSERTED_EVENTS,
            [],
        ];
    }

    /**
     * @param string[] $excludedEvents
     * @param list<array{0: string, 1: int, 2?: string}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $excludedEvents, array $expectedErrorMessagesWithLines): void
    {
        $this->excludedEvents = $excludedEvents;

        $this->analyse([self::FIXTURE], $expectedErrorMessagesWithLines);
    }

    /**
     * An enum without backed cases would otherwise report nothing at all.
     */
    public function testRuleReportsEnumWithoutBackedEvents(): void
    {
        $this->enumClass = UnbackedWebhookEventStub::class;

        $this->analyse([self::FIXTURE], [
            [
                \sprintf('No backed events are declared in %s, did the enum change?', UnbackedWebhookEventStub::class),
                1,
            ],
        ]);
    }

    /**
     * @return array<\PHPStan\Collectors\Collector<\PhpParser\Node, string>>
     */
    protected function getCollectors(): array
    {
        return [new AssertedWebhookEventCollector(WebhookStub::class)];
    }

    protected function getRule(): Rule
    {
        return new WebhookEventCoverageRule(
            $this->createReflectionProvider(),
            $this->enumClass,
            $this->excludedEvents,
        );
    }

    private static function getNotAssertedError(string $case, string $event): string
    {
        return \sprintf(
            "No test asserts the webhook event %s. Assert the webhook entity with `'event' => '%s'` in a test.",
            $case,
            $event,
        );
    }
}

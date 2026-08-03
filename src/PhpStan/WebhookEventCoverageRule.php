<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Reports every case of the given webhook event enum that is never asserted in the tests.
 *
 * The rule needs its collector registered alongside it, and only reports on a full-scope analysis:
 *
 * ```neon
 * services:
 *     -
 *         class: EonX\EasyQuality\PhpStan\AssertedWebhookEventCollector
 *         tags: [phpstan.collector]
 *
 *     -
 *         class: EonX\EasyQuality\PhpStan\WebhookEventCoverageRule
 *         tags: [phpstan.rules.rule]
 *         arguments:
 *             enumClass: App\Webhook\Enum\WebhookEvent
 *             excludedEvents:
 *                 - merchant:update
 * ```
 *
 * @implements \PHPStan\Rules\Rule<\PHPStan\Node\CollectedDataNode>
 */
final readonly class WebhookEventCoverageRule implements Rule
{
    private const string EXCLUSION_NOT_NEEDED_IDENTIFIER = 'easyQuality.webhookEventExclusionNotNeeded';

    private const string MISCONFIGURED_IDENTIFIER = 'easyQuality.webhookEventCoverageMisconfigured';

    private const string NOT_ASSERTED_IDENTIFIER = 'easyQuality.webhookEventNotAsserted';

    /**
     * @param string[] $excludedEvents Event values, such as `merchant:update`, that are knowingly not asserted
     */
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private string $enumClass,
        private array $excludedEvents = [],
    ) {}

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param \PHPStan\Node\CollectedDataNode $node
     *
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // Skip if it is not a full-scope check
        if ($node->isOnlyFilesAnalysis()) {
            return [];
        }

        // A missing class or a non-enum throws on purpose: a misconfigured rule must not report nothing
        $enum = $this->reflectionProvider->getClass($this->enumClass);
        $enumFile = (string)$enum->getFileName();
        $configuredEvents = [];

        foreach ($enum->getEnumCases() as $case) {
            // A backed case has exactly one constant string
            $event = ($case->getBackingValueType()?->getConstantStrings()[0] ?? null)?->getValue();

            if ($event !== null) {
                $configuredEvents[$event] = $case->getName();
            }
        }

        // Without backed cases there are no events, so every event would look asserted
        if ($configuredEvents === []) {
            return [
                self::getError(
                    \sprintf('No backed events are declared in %s, did the enum change?', $this->enumClass),
                    1,
                    $enumFile,
                    self::MISCONFIGURED_IDENTIFIER,
                ),
            ];
        }

        $assertedEvents = [];

        foreach ($node->get(AssertedWebhookEventCollector::class) as $eventsPerFile) {
            $assertedEvents += \array_flip($eventsPerFile);
        }

        $errors = [];

        foreach ($configuredEvents as $event => $caseName) {
            if (isset($assertedEvents[$event]) || \in_array($event, $this->excludedEvents, true)) {
                continue;
            }

            $errors[] = self::getError(
                \sprintf(
                    "No test asserts the webhook event %s. Assert the webhook entity with `'event' => '%s'`"
                    . ' in a test.',
                    $caseName,
                    $event,
                ),
                self::getEventLine($event, $enumFile),
                $enumFile,
                self::NOT_ASSERTED_IDENTIFIER,
            );
        }

        return \array_merge($errors, $this->getStaleExclusionErrors($configuredEvents, $assertedEvents, $enumFile));
    }

    private static function getError(
        string $message,
        int $line,
        string $enumFile,
        string $identifier,
    ): RuleError {
        return RuleErrorBuilder::message($message)
            ->identifier($identifier)
            ->file($enumFile)
            ->line($line)
            ->build();
    }

    private static function getEventLine(string $event, string $enumFile): int
    {
        $pattern = \sprintf("/= '%s';/", \preg_quote($event, '/'));
        $matchedLines = \preg_grep($pattern, (array)\file($enumFile));

        // The first matching line wins, line 1 when the event is not in the enum at all
        return (int)\array_key_first($matchedLines === false ? [] : $matchedLines) + 1;
    }

    /**
     * An exclusion must be dropped once its event is asserted or gone, otherwise the list never shrinks.
     *
     * @param array<string, string> $configuredEvents
     * @param array<string, int> $assertedEvents
     *
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function getStaleExclusionErrors(array $configuredEvents, array $assertedEvents, string $enumFile): array
    {
        $errors = [];

        foreach ($this->excludedEvents as $excludedEvent) {
            $isConfigured = isset($configuredEvents[$excludedEvent]);

            if ($isConfigured && isset($assertedEvents[$excludedEvent]) === false) {
                continue;
            }

            $errors[] = self::getError(
                \sprintf(
                    'The excluded webhook event %s is %s. Remove it from the excluded events.',
                    $excludedEvent,
                    $isConfigured ? 'asserted by a test' : 'not a case of the event enum',
                ),
                self::getEventLine($excludedEvent, $enumFile),
                $enumFile,
                self::EXCLUSION_NOT_NEEDED_IDENTIFIER,
            );
        }

        return $errors;
    }
}

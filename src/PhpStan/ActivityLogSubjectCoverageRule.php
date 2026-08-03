<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Throwable;

/**
 * Reports every subject configured in the given easy-activity config file that no test asserts.
 *
 * The rule needs its collector registered alongside it, and only reports on a full-scope analysis:
 *
 * ```neon
 * services:
 *     -
 *         class: EonX\EasyQuality\PhpStan\AssertedActivityLogSubjectCollector
 *         tags: [phpstan.collector]
 *
 *     -
 *         class: EonX\EasyQuality\PhpStan\ActivityLogSubjectCoverageRule
 *         tags: [phpstan.rules.rule]
 *         arguments:
 *             configFile: %currentWorkingDirectory%/config/packages/easy_activity.php
 *             excludedSubjects:
 *                 - App\Entity\Something
 * ```
 *
 * @implements \PHPStan\Rules\Rule<\PHPStan\Node\CollectedDataNode>
 */
final readonly class ActivityLogSubjectCoverageRule implements Rule
{
    /**
     * The config file calls this helper, which projects declare in `config/reference.php` and do not autoload.
     */
    private const string CONFIG_HELPER_CLASS = 'Symfony\Component\DependencyInjection\Loader\Configurator\App';

    private const string EXCLUSION_NOT_NEEDED_IDENTIFIER = 'easyQuality.activityLogSubjectExclusionNotNeeded';

    private const string MISCONFIGURED_IDENTIFIER = 'easyQuality.activityLogSubjectCoverageMisconfigured';

    private const string NOT_ASSERTED_IDENTIFIER = 'easyQuality.activityLogSubjectNotAsserted';

    /**
     * @param string[] $excludedSubjects Subject classes that are knowingly not asserted
     */
    public function __construct(private string $configFile, private array $excludedSubjects = []) {}

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

        // Requiring a missing file is a fatal error, and RuleErrorBuilder only anchors to existing files
        if (\is_file($this->configFile) === false) {
            return [
                RuleErrorBuilder::message(
                    \sprintf('The configured activity log config file %s does not exist.', $this->configFile),
                )
                    ->identifier(self::MISCONFIGURED_IDENTIFIER)
                    ->build(),
            ];
        }

        try {
            $configuredSubjects = $this->getConfiguredSubjects();
        } catch (Throwable $throwable) {
            // A throwing config file would otherwise abort the analysis as an internal error
            return [
                $this->getError(
                    \sprintf('The activity log config file cannot be loaded: %s.', $throwable->getMessage()),
                    1,
                    self::MISCONFIGURED_IDENTIFIER,
                ),
            ];
        }

        if ($configuredSubjects === []) {
            return [
                $this->getError(
                    'No activity log subjects are configured, did the config structure change?',
                    1,
                    self::MISCONFIGURED_IDENTIFIER,
                ),
            ];
        }

        $assertedSubjects = [];

        foreach ($node->get(AssertedActivityLogSubjectCollector::class) as $subjectsPerFile) {
            foreach ($subjectsPerFile as $subjects) {
                $assertedSubjects += \array_flip($subjects);
            }
        }

        $subjectLines = $this->getSubjectLines();
        $errors = [];

        foreach ($configuredSubjects as $configuredSubject) {
            if (isset($assertedSubjects[$configuredSubject])) {
                continue;
            }

            if (\in_array($configuredSubject, $this->excludedSubjects, true)) {
                continue;
            }

            $errors[] = $this->getError(
                \sprintf(
                    'No test asserts the activity log of the configured subject %s.'
                    . ' Add an `%s()` assertion for it.',
                    $configuredSubject,
                    AssertedActivityLogSubjectCollector::METHOD_NAME,
                ),
                $subjectLines[$configuredSubject] ?? 1,
                self::NOT_ASSERTED_IDENTIFIER,
            );
        }

        return \array_merge(
            $errors,
            $this->getStaleExclusionErrors($configuredSubjects, $assertedSubjects, $subjectLines),
        );
    }

    /**
     * The helper lives in `config/reference.php`, at an unknown depth above the config file: an environment
     * config, such as `config/packages/test/x.php`, sits one level deeper than a regular one.
     */
    private static function requireReferenceFile(string $configFile): void
    {
        $directory = \dirname($configFile);

        while ($directory !== \dirname($directory)) {
            $referenceFile = $directory . '/reference.php';

            if (\is_file($referenceFile)) {
                require_once $referenceFile;

                return;
            }

            $directory = \dirname($directory);
        }
    }

    /**
     * @return string[]
     */
    private function getConfiguredSubjects(): array
    {
        if (\class_exists(self::CONFIG_HELPER_CLASS) === false) {
            self::requireReferenceFile($this->configFile);
        }

        /** @var array{easy_activity?: array{subjects?: array<string, array<string, string|string[]>>}} $config */
        $config = require $this->configFile;

        return \array_keys($config['easy_activity']['subjects'] ?? []);
    }

    private function getError(string $message, int $line, string $identifier): RuleError
    {
        return RuleErrorBuilder::message($message)
            ->identifier($identifier)
            ->file($this->configFile)
            ->line($line)
            ->build();
    }

    /**
     * @param string[] $configuredSubjects
     * @param array<string, int> $assertedSubjects
     * @param array<string, int> $subjectLines
     *
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function getStaleExclusionErrors(
        array $configuredSubjects,
        array $assertedSubjects,
        array $subjectLines,
    ): array {
        $errors = [];

        foreach ($this->excludedSubjects as $excludedSubject) {
            $isConfigured = \in_array($excludedSubject, $configuredSubjects, true);

            if ($isConfigured && isset($assertedSubjects[$excludedSubject]) === false) {
                continue;
            }

            $errors[] = $this->getError(
                \sprintf(
                    'The excluded subject %s is %s. Remove it from the excluded subjects.',
                    $excludedSubject,
                    $isConfigured ? 'asserted by a test' : 'not a configured subject',
                ),
                $subjectLines[$excludedSubject] ?? 1,
                self::EXCLUSION_NOT_NEEDED_IDENTIFIER,
            );
        }

        return $errors;
    }

    /**
     * The config declares its subjects by short name, so the class names are resolved from the imports.
     *
     * @return array<string, int> Subject class => line of the `X::class` declaring it
     */
    private function getSubjectLines(): array
    {
        $statements = new ParserFactory()->createForNewestSupportedVersion()
            ->parse((string)\file_get_contents($this->configFile)) ?? [];
        $statements = new NodeTraverser(new NameResolver())->traverse($statements);
        $lines = [];

        foreach (new NodeFinder()->findInstanceOf($statements, ClassConstFetch::class) as $classConstFetch) {
            if ($classConstFetch->class instanceof Name
                && $classConstFetch->name instanceof Identifier
                && $classConstFetch->name->toLowerString() === 'class') {
                // The first `X::class` of a subject wins
                $lines[$classConstFetch->class->toString()] ??= $classConstFetch->getStartLine();
            }
        }

        return $lines;
    }
}

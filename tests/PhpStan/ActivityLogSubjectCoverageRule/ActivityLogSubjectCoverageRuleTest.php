<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule;

use EonX\EasyQuality\PhpStan\ActivityLogSubjectCoverageRule;
use EonX\EasyQuality\PhpStan\AssertedActivityLogSubjectCollector;
use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\AssertedSubjectStub;
use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\NotAssertedSubjectStub;
use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\SubNotAssertedSubjectStub;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\EonX\EasyQuality\PhpStan\ActivityLogSubjectCoverageRule>
 */
final class ActivityLogSubjectCoverageRuleTest extends RuleTestCase
{
    private const string BROKEN_CONFIG_FILE = __DIR__ . '/Fixture/config/packages/broken_activity.php';

    private const string COLLIDING_CONFIG_FILE = __DIR__ . '/Fixture/config/packages/colliding_activity.php';

    private const string CONFIG_FILE = __DIR__ . '/Fixture/config/packages/easy_activity.php';

    private const string FIXTURE = __DIR__ . '/Fixture/AssertsOneSubject.php';

    private const string MISSING_CONFIG_FILE = __DIR__ . '/Fixture/config/packages/does_not_exist.php';

    private string $configFile = self::CONFIG_FILE;

    /**
     * @var string[]
     */
    private array $excludedSubjects = [];

    /**
     * @see testRule
     */
    public static function provideData(): iterable
    {
        yield 'reports the configured subject that no test asserts' => [
            [],
            [
                [self::getNotAssertedError(NotAssertedSubjectStub::class), 13],
            ],
        ];

        yield 'reports the excluded subject that is asserted or not configured' => [
            [AssertedSubjectStub::class, 'App\\Unknown\\Subject'],
            [
                [
                    'The excluded subject App\\Unknown\\Subject is not a configured subject.'
                    . ' Remove it from the excluded subjects.',
                    1,
                ],
                [
                    'The excluded subject ' . AssertedSubjectStub::class . ' is asserted by a test.'
                    . ' Remove it from the excluded subjects.',
                    10,
                ],
                [self::getNotAssertedError(NotAssertedSubjectStub::class), 13],
            ],
        ];

        yield 'reports nothing when the not asserted subject is excluded' => [
            [NotAssertedSubjectStub::class],
            [],
        ];
    }

    /**
     * @param string[] $excludedSubjects
     * @param list<array{0: string, 1: int, 2?: string}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $excludedSubjects, array $expectedErrorMessagesWithLines): void
    {
        $this->excludedSubjects = $excludedSubjects;

        $this->analyse([self::FIXTURE], $expectedErrorMessagesWithLines);
    }

    /**
     * A subject whose short name is a suffix of another one must still be anchored to its own line.
     */
    public function testRuleAnchorsSubjectsWithCollidingShortNames(): void
    {
        $this->configFile = self::COLLIDING_CONFIG_FILE;

        $this->analyse([self::FIXTURE], [
            [self::getNotAssertedError(SubNotAssertedSubjectStub::class), 11],
            [self::getNotAssertedError(NotAssertedSubjectStub::class), 14],
        ]);
    }

    /**
     * A throwing config file would otherwise abort the analysis as an internal error.
     */
    public function testRuleReportsConfigFileThatCannotBeLoaded(): void
    {
        $this->configFile = self::BROKEN_CONFIG_FILE;

        $this->analyse([self::FIXTURE], [
            [
                'The activity log config file cannot be loaded: Class "MissingConfigHelper" not found.',
                1,
            ],
        ]);
    }

    /**
     * The error is not anchored to a file, hence line -1.
     */
    public function testRuleReportsMissingConfigFile(): void
    {
        $this->configFile = self::MISSING_CONFIG_FILE;

        $this->analyse([self::FIXTURE], [
            [
                \sprintf('The configured activity log config file %s does not exist.', self::MISSING_CONFIG_FILE),
                -1,
            ],
        ]);
    }

    /**
     * @return array<\PHPStan\Collectors\Collector<\PhpParser\Node, string[]>>
     */
    protected function getCollectors(): array
    {
        return [new AssertedActivityLogSubjectCollector()];
    }

    protected function getRule(): Rule
    {
        return new ActivityLogSubjectCoverageRule($this->configFile, $this->excludedSubjects);
    }

    private static function getNotAssertedError(string $subject): string
    {
        return \sprintf(
            'No test asserts the activity log of the configured subject %s.'
            . ' Add an `assertActivityLog()` assertion for it.',
            $subject,
        );
    }
}

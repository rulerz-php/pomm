<?php

declare(strict_types=1);

namespace Tests\RulerZ\Matcher;

use PhpSpec\Exception\Example\FailureException;

trait FilterResultMatcherTrait
{
    public function getMatchers(): array
    {
        return [
            'returnResults' => function ($subject, iterable $expectedResults): bool {
                if (!$subject instanceof \Traversable) {
                    throw new FailureException('The method did not return a \Traversable object');
                }

                $receivedResults = iterator_to_array($subject);

                if (count($receivedResults) !== count($expectedResults)) {
                    throw new FailureException(sprintf(
                        'Expected %d result, got %d',
                        count($expectedResults),
                        count($receivedResults)
                    ));
                }

                foreach ($receivedResults as $i => $result) {
                    $expectedResult = $expectedResults[$i];

                    if ($result !== $expectedResult) {
                        throw new FailureException(sprintf(
                            "Wrong result %d:\nExpected:\n%s\nActual:\n%s",
                            $i,
                            var_export($expectedResults, true),
                            var_export($receivedResults, true)
                        ));
                    }
                }

                return true;
            },
        ];
    }
}

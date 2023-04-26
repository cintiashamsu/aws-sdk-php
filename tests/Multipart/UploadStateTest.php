<?php
namespace Aws\Test\Multipart;

use Aws\Multipart\UploadState;
use PHPUnit\Framework\TestCase;

/**
 * @covers Aws\Multipart\UploadState
 */
class UploadStateTest extends TestCase
{
    public function testCanManageStatusAndUploadId()
    {
        $state = new UploadState(['a' => true]);
        $this->assertArrayHasKey('a', $state->getId());
        // Note: the state should not be initiated at first.
        $this->assertFalse($state->isInitiated());
        $this->assertFalse($state->isCompleted());

        $state->setUploadId('b', true);
        $this->assertArrayHasKey('b', $state->getId());
        $this->assertArrayHasKey('a', $state->getId());

        $state->setStatus(UploadState::INITIATED);
        $this->assertFalse($state->isCompleted());
        $this->assertTrue($state->isInitiated());

        $state->setStatus(UploadState::COMPLETED);
        $this->assertFalse($state->isInitiated());
        $this->assertTrue($state->isCompleted());
    }

    public function testCanStorePartSize()
    {
        $state = new UploadState([]);
        $this->assertNull($state->getPartSize());
        $state->setPartSize(50000000);
        $this->assertSame(50000000, $state->getPartSize());
    }

    public function testCanTrackUploadedParts()
    {
        $state = new UploadState([]);
        $this->assertEmpty($state->getUploadedParts());

        $state->markPartAsUploaded(1, ['foo' => 1]);
        $state->markPartAsUploaded(3, ['foo' => 3]);
        $state->markPartAsUploaded(2, ['foo' => 2]);

        $this->assertTrue($state->hasPartBeenUploaded(2));
        $this->assertFalse($state->hasPartBeenUploaded(5));

        // Note: The parts should come out sorted.
        $this->assertSame([1, 2, 3], array_keys($state->getUploadedParts()));
    }

    public function testSerializationWorks()
    {
        $state = new UploadState([]);
        $state->setPartSize(5);
        $state->markPartAsUploaded(1);
        $state->setStatus($state::INITIATED);
        $state->setUploadId('foo', 'bar');
        $serializedState = serialize($state);

        /** @var UploadState $newState */
        $newState = unserialize($serializedState);
        $this->assertSame(5, $newState->getPartSize());
        $this->assertArrayHasKey(1, $state->getUploadedParts());
        $this->assertTrue($newState->isInitiated());
        $this->assertArrayHasKey('foo', $newState->getId());
    }
    /**
     * @dataProvider getDisplayProgressCases
     */
    public function testDisplayProgressPrints(
        $totalSize,
        $totalUploaded,
        $progressBar
    ) {
        $state = new UploadState([]);
        $state->setProgressThresholds($totalSize);
        $state->displayProgress($totalUploaded);

        $this->expectOutputString($progressBar);
    }

    public function getDisplayProgressCases()
    {
        $progressBar = [
            "Transfer initiated...\n|                    | 0.0%\n",
            "|==                  | 12.5%\n",
            "|=====               | 25.0%\n",
            "|=======             | 37.5%\n",
            "|==========          | 50.0%\n",
            "|============        | 62.5%\n",
            "|===============     | 75.0%\n",
            "|=================   | 87.5%\n",
            "|====================| 100.0%\nTransfer complete!\n"
        ];
        return [
            [100000, 0, $progressBar[0]],
            [100000, 12499, $progressBar[0]],
            [100000, 12500, "$progressBar[0]$progressBar[1]"],
            [100000, 100000, implode($progressBar)]
        ];
    }

    /**
     * @dataProvider getThresholdCases
     */
    public function testUploadThresholds($totalSize)
    {
        $state = new UploadState([]);
        $threshold = $state->setProgressThresholds($totalSize);

        $this->assertIsArray($threshold);
        $this->assertCount(8, $threshold);
    }

    public function getThresholdCases()
    {
        return [
            [0],
            [100000],
            [100001]
        ];
    }
}
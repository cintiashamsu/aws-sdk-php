<?php
namespace Aws\Test\Multipart;

use Aws\Multipart\DownloadState;
use PHPUnit\Framework\TestCase;

/**
 * @covers Aws\Multipart\DownloadState
 */
class DownloadStateTest extends TestCase
{
    public function testCanManageStatusAndDownloadId()
    {
        $state = new DownloadState(['a' => true]);
        $this->assertArrayHasKey('a', $state->getId());
        // Note: the state should not be initiated at first.
        $this->assertFalse($state->isInitiated());
        $this->assertFalse($state->isCompleted());

        $state->setUploadId('b', true);
        $this->assertArrayHasKey('b', $state->getId());
        $this->assertArrayHasKey('a', $state->getId());

        $state->setStatus(DownloadState::INITIATED);
        $this->assertFalse($state->isCompleted());
        $this->assertTrue($state->isInitiated());

        $state->setStatus(DownloadState::COMPLETED);
        $this->assertFalse($state->isInitiated());
        $this->assertTrue($state->isCompleted());
    }

    public function testCanStorePartSize()
    {
        $state = new DownloadState([]);
        $this->assertNull($state->getPartSize());
        $state->setPartSize(50000000);
        $this->assertSame(50000000, $state->getPartSize());
    }

    public function testCanTrackDownloadedParts()
    {
        $state = new DownloadState([]);
        $this->assertEmpty($state->getDownloadedParts());

        $state->markPartAsDownloaded(1, ['foo' => 1]);
        $state->markPartAsDownloaded(3, ['foo' => 3]);
        $state->markPartAsDownloaded(2, ['foo' => 2]);

        $this->assertTrue($state->hasPartBeenDownloaded(2));
        $this->assertFalse($state->hasPartBeenDownloaded(5));

        // Note: The parts should come out sorted.
        $this->assertSame([1, 2, 3], array_keys($state->getDownloadedParts()));
    }

    public function testSerializationWorks()
    {
        $state = new DownloadState([]);
        $state->setPartSize(5);
        $state->markPartAsDownloaded(1);
        $state->setStatus($state::INITIATED);
        $state->setUploadId('foo', 'bar');
        $serializedState = serialize($state);

        /** @var DownloadState $newState */
        $newState = unserialize($serializedState);
        $this->assertSame(5, $newState->getPartSize());
        $this->assertArrayHasKey(1, $state->getDownloadedParts());
        $this->assertTrue($newState->isInitiated());
        $this->assertArrayHasKey('foo', $newState->getId());
    }
}
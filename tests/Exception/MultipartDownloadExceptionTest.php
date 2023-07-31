<?php
namespace Aws\Test\Exception;

use Aws\Command;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartDownloadException;
use Aws\Multipart\DownloadState;
use PHPUnit\Framework\TestCase;

/**
 * @covers Aws\Exception\MultipartDownloadException
 */
class MultipartDownloadExceptionTest extends TestCase
{
    /**
     * @dataProvider getTestCases
     */
    public function testCanCreateMultipartException($commandName, $status)
    {
        $msg = 'Error encountered while reticulating splines.';
        $state = new DownloadState([]);
        $prev = new AwsException($msg, new Command($commandName));
        $exception = new MultipartDownloadException($state, $prev);

        $this->assertSame(
            "An exception occurred while {$status} a multipart upload: $msg",
            $exception->getMessage()
        );
        $this->assertSame($state, $exception->getState());
        $this->assertSame($prev, $exception->getPrevious());
    }

    public function getTestCases()
    {
        return [
            ['GetObject', 'performing']
        ];
    }

    public function testCanCreateExceptionListingFailedParts()
    {
        $state = new DownloadState([]);
        $failed = [
            1 => new AwsException('Bad digest.', new Command('GetObject')),
            5 => new AwsException('Missing header.', new Command('GetObject')),
            8 => new AwsException('Needs more love.', new Command('GetObject')),
        ];

        $exception = new MultipartDownloadException($state, $failed);

        $expected = <<<MSG
An exception occurred while uploading parts to a multipart upload. The following parts had errors:
- Part 1: Bad digest.
- Part 5: Missing header.
- Part 8: Needs more love.

MSG;

        $this->assertSame($expected, $exception->getMessage());
    }
}

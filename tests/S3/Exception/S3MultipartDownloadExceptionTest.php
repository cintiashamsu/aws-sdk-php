<?php
namespace Aws\Test\S3\Exception;

use Aws\Command;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3MultipartDownloadException;
use Aws\Multipart\DownloadState;
use GuzzleHttp\Psr7;
use PHPUnit\Framework\TestCase;

/**
 * @covers Aws\S3\Exception\S3MultipartDownloadException
 */
class S3MultipartDownloadExceptionTest extends TestCase
{
    public function testCanProviderFailedTransferFilePathInfo()
    {
        $state = new DownloadState([]);
        $failed = [
            1 => new AwsException('Bad digest.', new Command('GetObject', [
                'Bucket' => 'foo',
                'Key' => 'bar'
            ])),
            5 => new AwsException('Missing header.', new Command('GetObject', [
                'Bucket' => 'foo',
                'Key' => 'bar'
            ])),
            8 => new AwsException('Needs more love.', new Command('GetObject')),
        ];

        $path = '/path/to/the/large/file/test.zip';
        $exception = new S3MultipartDownloadException($state, $failed);
        $this->assertSame('foo', $exception->getBucket());
        $this->assertSame('bar', $exception->getKey());
//        $this->assertSame('php://temp', $exception->getSourceFileName());
    }
}

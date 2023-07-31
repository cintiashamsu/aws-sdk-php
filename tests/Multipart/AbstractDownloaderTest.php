<?php
namespace Aws\Test\Multipart;

use Aws\Command;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartDownloadException;
use Aws\S3\MultipartDownloader;
use Aws\Multipart\DownloadState;
use Aws\Result;
use Aws\Test\UsesServiceTrait;
use GuzzleHttp\Psr7;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @covers Aws\Multipart\AbstractDownloader
 */
class AbstractDownloaderTest extends TestCase
{
    use UsesServiceTrait;

    private function getDownloaderWithState($status, array $results = [], $source = null)
    {
        $state = new DownloadState(['Bucket' => 'foo', 'Key' => 'bar']);
        $state->setPartSize(2);
        $state->setStatus($status);

        return $this->getTestDownloader(
            ['state' => $state],
            $results
        );
    }

    private function getTestDownloader(
        array $config = [],
        array $results = []
    ) {
        $client = $this->getTestClient('s3', [
            'validate' => false,
            'retries'  => 0,
        ]);
        $this->addMockResults($client, $results);

        return new MultipartDownloader($client, 'php://temp', $config);
    }

    public function testThrowsExceptionOnBadInitiateRequest()
    {
        $this->expectException(\Aws\S3\Exception\S3MultipartDownloadException::class);
        $downloader = $this->getDownloaderWithState(DownloadState::CREATED, [
            new AwsException('Failed', new Command('Initiate')),
        ]);
        $downloader->download();
    }

    public function testThrowsExceptionIfStateIsCompleted()
    {
        // set exception to expect
        // set state as completed
        // make sure state is completed
        // check that exception is thrown
        $this->expectException(\LogicException::class);
        $downloader = $this->getDownloaderWithState(DownloadState::COMPLETED);
        $this->assertTrue($downloader->getState()->isCompleted());
        $downloader->download();
    }

    public function testSuccessfulCompleteReturnsResult()
    {
        //
        $downloader = $this->getDownloaderWithState(DownloadState::CREATED, [
            new Result(['body' => Psr7\Utils::streamFor(str_repeat('.', 1 * 1048576))])
        ], 'php://temp');
        $this->assertSame(str_repeat('.', 1 * 1048576), $downloader->download()['body']);
        $this->assertTrue($downloader->getState()->isCompleted());
    }

    public function testThrowsExceptionOnBadCompleteRequest()
    {
        $this->expectException(\Aws\S3\Exception\S3MultipartDownloadException::class);
        $uploader = $this->getDownloaderWithState(DownloadState::CREATED, [
            new Result(), // Initiate
            new Result(), // Upload
            new AwsException('Failed', new Command('Complete')),
        ], 'php://temp');
        $uploader->download();
    }

    public function testThrowsExceptionOnBadUploadRequest()
    {
        $uploader = $this->getDownloaderWithState(DownloadState::CREATED, [
            new Result(), // Initiate
            new AwsException('Failed[1]', new Command('Upload', ['PartNumber' => 1])),
            new Result(), // Upload
            new Result(), // Upload
            new AwsException('Failed[4]', new Command('Upload', ['PartNumber' => 4])),
            new Result(), // Upload
        ], Psr7\Utils::streamFor('abcdefghi'));

        try {
            $uploader->download();
            $this->fail('No exception was thrown.');
        } catch (MultipartDownloadException $e) {
            $message = $e->getMessage();
            $this->assertStringContainsString('Failed[1]', $message);
            $this->assertStringContainsString('Failed[4]', $message);
            $uploadedParts = $e->getState()->getDownloadedParts();
            $this->assertCount(3, $uploadedParts);
            $this->assertArrayHasKey(2, $uploadedParts);
            $this->assertArrayHasKey(3, $uploadedParts);
            $this->assertArrayHasKey(5, $uploadedParts);

            // Test if can resume an upload.
            $serializedState = serialize($e->getState());
            $state = unserialize($serializedState);
            $secondChance = $this->getTestDownloader(
                Psr7\Utils::streamFor('abcdefghi'),
                ['state' => $state],
                [
                    new Result(), // Upload
                    new Result(), // Upload
                    new Result(['foo' => 'bar']), // Upload
                ]
            );
            $result = $secondChance->upload();
            $this->assertSame('bar', $result['foo']);
        }
    }

    public function testAsyncUpload()
    {
        $called = 0;
        $fn = function () use (&$called) {
            $called++;
        };

        $uploader = $this->getTestDownloader(Psr7\Utils::streamFor('abcde'), [
            'bucket'              => 'foo',
            'key'                 => 'bar',
            'prepare_data_source' => $fn,
            'before_initiate'     => $fn,
            'before_upload'       => $fn,
            'before_complete'     => $fn,
        ], [
            new Result(), // Initiate
            new Result(), // Upload
            new Result(), // Upload
            new Result(), // Upload
            new Result(['test' => 'foo']) // Complete
        ]);

        $promise = $uploader->promise();
        $this->assertSame($promise, $uploader->promise());
        $this->assertInstanceOf('Aws\Result', $promise->wait());
        $this->assertSame(6, $called);
    }

    public function testRequiresIdParams()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getTestDownloader();
    }

    /**
     * @param bool        $seekable
     * @param DownloadState $state
     * @param array       $expectedBodies
     *
     * @dataProvider getPartGeneratorTestCases
     */
    public function testCommandGeneratorYieldsExpectedUploadCommands(
        $seekable,
        DownloadState $state,
        array $expectedBodies
    ) {
        $source = Psr7\Utils::streamFor(fopen(__DIR__ . '/source.txt', 'r'));
        if (!$seekable) {
            $source = new Psr7\NoSeekStream($source);
        }

        $uploader = $this->getTestDownloader($source, ['state' => $state]);
        $uploader->getState();
        $handler = function (callable $handler) {
            return function ($c, $r) use ($handler) {
                return $handler($c, $r);
            };
        };

        $actualBodies = [];
        $getUploadCommands = (new \ReflectionObject($uploader))
            ->getMethod('getDownloadCommands');
        $getUploadCommands->setAccessible(true);
        foreach ($getUploadCommands->invoke($uploader, $handler) as $cmd) {
            $actualBodies[$cmd['PartNumber']] = $cmd['Body']->getContents();
        }

        $this->assertEquals($expectedBodies, $actualBodies);
    }

    public function getPartGeneratorTestCases()
    {
        $expected = [
            1 => 'AA',
            2 => 'BB',
            3 => 'CC',
            4 => 'DD',
            5 => 'EE',
            6 => 'F' ,
        ];
        $expectedSkip = $expected;
        unset($expectedSkip[1], $expectedSkip[2], $expectedSkip[4]);
        $state = new DownloadState([]);
        $state->setPartSize(2);
        $stateSkip = clone $state;
        $stateSkip->markPartAsDownloaded(1);
        $stateSkip->markPartAsDownloaded(2);
        $stateSkip->markPartAsDownloaded(4);
        return [
            [true,  $state,     $expected],
            [false, $state,     $expected],
            [true,  $stateSkip, $expectedSkip],
            [false, $stateSkip, $expectedSkip],
        ];
    }
}

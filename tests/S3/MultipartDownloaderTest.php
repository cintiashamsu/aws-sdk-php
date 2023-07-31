<?php
namespace Aws\Test\S3;

use Aws\Middleware;
use Aws\S3\MultipartDownloader;
use Aws\Result;
use Aws\S3\S3Client;
use Aws\Test\UsesServiceTrait;
use Aws\S3\CalculatesChecksumTrait;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @covers Aws\S3\MultipartDownloader
 */
class MultipartDownloaderTest extends TestCase
{
//    tests:
//    - workflow for each of the config options
//    - testing each method in multipart downloader
//          - createPart, setStreamPositionArray, createDestStream
//    -

    use UsesServiceTrait;
    use CalculatesChecksumTrait;

    const MB = 1048576;
    const FILENAME = '_aws-sdk-php-s3-mup-test-dots.txt';

    public static function tear_down_after_class()
    {
        @unlink(sys_get_temp_dir() . '/' . self::FILENAME);
    }

    /**
     * @dataProvider getTestCases
     */
    public function testS3MultipartDownloadWorkflow(
        array $uploadOptions = [],
        $error = false
    ) {
        $client = $this->getTestClient('s3');
        $this->addMockResults($client, [
            new Result(['Body' => Psr7\Utils::streamFor(str_repeat('.', 10 * self::MB)),
                        'ChecksumValidated' => 'CRC32',
//                        'ChecksumCRC32' =>
//                            CalculatesChecksumTrait::getEncodedValue('crc32',
//                                Psr7\Utils::streamFor(str_repeat('.', 10 * self::MB)))
                        'ChecksumCRC32' => 'M6FqCg=='
            ])
        ]);

        if ($error) {
            if (method_exists($this, 'expectException')) {
                $this->expectException($error);
            } else {
                $this->setExpectedException($error);
            }
        }

        $filename = tmpfile();
        $dest = stream_get_meta_data($filename)['uri'];
        $downloader = new MultipartDownloader($client, $dest, $uploadOptions);
        $result = $downloader->download();
        $output = file_get_contents($dest);

        $this->assertStringContainsString(str_repeat('.', 10 * self::MB), $output);
        $this->assertTrue(filesize($dest) == 10*self::MB);
        $this->assertTrue($downloader->getState()->isCompleted());
    }

    public function getTestCases()
    {
        $defaults = [
            'bucket' => 'foo',
            'key'    => 'bar'
        ];

        return [
            [
                ['acl' => 'private'] + $defaults
            ],
            [
                ['MultipartDownloadType' => 'Range'] + $defaults
            ],
            [
                ['MultipartDownloadType' => 'Parts'] + $defaults
            ],
            [
                ['PartNumber' => '1'] + $defaults
            ],
            [
                ['Range' => 'bytes=0-100'] + $defaults
            ],
            [
                ['checksum_validation_enabled' => false] + $defaults
            ],
            [
                ['checksum_validation_enabled' => true] + $defaults
            ]
        ];
    }

    // continuing a prev download?
    public function testCanLoadStateFromDownload()
    {
        $client = $this->getTestClient('s3');
        $this->addMockResults($client, [
            new Result(['ETag' => 'A',
                        'ChecksumValidated' => 'CRC32',
                        'ContentLength' => 3 * self::MB])
        ]);

        $size = 1 * self::MB;
        $data = str_repeat('.', $size);
        file_put_contents('php://memory', $data);

        $state = MultipartDownloader::getStateFromService($client, 'foo', 'bar', 'php://memory');
        $downloader = new MultipartDownloader($client, $dest, ['state' => $state]);
        $downloader->download();

        $this->assertTrue($downloader->getState()->isCompleted());
//        $this->assertSame(4 * self::MB, $downloader->getState()->getPartSize());
//        $this->assertSame($url, $result['ObjectURL']);
    }

    public function testCanUseCaseInsensitiveConfigKeys()
    {
        $client = $this->getTestClient('s3');
        $putObjectMup = new MultipartDownloader($client, 'php://temp', [
            'Bucket' => 'bucket',
            'Key' => 'key',
        ]);
        $classicMup = new MultipartDownloader($client, 'php://temp', [
            'bucket' => 'bucket',
            'key' => 'key',
        ]);
        $configProp = (new \ReflectionClass(MultipartDownloader::class))
            ->getProperty('config');
        $configProp->setAccessible(true);

        $this->assertSame($configProp->getValue($classicMup), $configProp->getValue($putObjectMup));
    }

    /** @doesNotPerformAssertions */
    public function testMultipartSuccessStreams()
    {
        $size = 12 * self::MB;
        $data = str_repeat('.', $size);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);

        return [
            [ // Seekable stream, regular config
                'php://temp',
                $size,
            ],
            [ // Non-seekable stream
                'php://temp',
                $size,
            ]
        ];
    }

    /**
     * @dataProvider testMultipartSuccessStreams
     */
    public function testS3MultipartDownloadParams($dest, $size)
    {
        /** @var \Aws\S3\S3Client $client */
        $client = $this->getTestClient('s3');
        $client->getHandlerList()->appendSign(
            Middleware::tap(function ($cmd, $req) {
                $name = $cmd->getName();
                if ($name === 'GetObject') {
                    $this->assertTrue(
                        $req->hasHeader('Content-MD5')
                    );
                }
            })
        );
        $uploadOptions = [
            'bucket'          => 'foo',
            'key'             => 'bar',
            'add_content_md5' => true,
//            'params'          => [
//                'RequestPayer'  => 'test',
//                'ContentLength' => $size
//            ],
//            'before_initiate' => function($command) {
//                $this->assertSame('test', $command['RequestPayer']);
//            },
//            'before_download'   => function($command) use ($size) {
//                $this->assertLessThan($size, $command['ContentLength']);
//                $this->assertSame('test', $command['RequestPayer']);
//            },
//            'before_complete' => function($command) {
//                $this->assertSame('test', $command['RequestPayer']);
//            },
            'checksum_validation_enabled' => false
        ];
        $url = 'http://foo.s3.amazonaws.com/bar';

        $this->addMockResults($client, [
            new Result(['PartNumber' => 1, 'ETag' => 'A', 'Body' => 'foobar',
                        'ChecksumValidated' => 'CRC32',
//                        'ChecksumCRC32' => CalculatesChecksumTrait::getEncodedValue('crc32', 'foobar')
]),
            new Result(['PartNumber' => 2, 'ETag' => 'B', 'Body' => 'foobar2',
                        'ChecksumValidated' => 'CRC32',
//                        'ChecksumCRC32' => CalculatesChecksumTrait::getEncodedValue('crc32', 'foobar2')
            ])
        ]);
        $filename = tmpfile();
        $dest = stream_get_meta_data($filename)['uri'];
        $uploader = new MultipartDownloader($client, $dest, $uploadOptions);
        $result = $uploader->download();
        print_r($result);
        $this->assertTrue($uploader->getState()->isCompleted());
    }

    public function getContentTypeSettingTests()
    {
        $size = 12 * self::MB;
        $data = str_repeat('.', $size);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);

        return [
            [ // Successful lookup from filename via stream
                Psr7\Utils::streamFor(fopen($filename, 'r')),
                [],
                'text/plain'
            ],
            [ // Unsuccessful lookup because of no file name
                Psr7\Utils::streamFor($data),
                [],
                'application/octet-stream'
            ],
            [ // Successful override of known type from filename
                Psr7\Utils::streamFor(fopen($filename, 'r')),
                ['ContentType' => 'TestType'],
                'TestType'
            ],
            [ // Successful override of unknown type
                Psr7\Utils::streamFor($data),
                ['ContentType' => 'TestType'],
                'TestType'
            ]
        ];
    }

    /**
     * @dataProvider getContentTypeSettingTests
     */
    public function testS3MultipartContentTypeSetting(
        $stream,
        $params,
        $expectedContentType
    ) {
        /** @var \Aws\S3\S3Client $client */
        $client = $this->getTestClient('s3');
        $uploadOptions = [
            'bucket'          => 'foo',
            'key'             => 'bar',
            'params'          => $params,
            'before_initiate' => function($command) use ($expectedContentType) {
                $this->assertEquals(
                    $expectedContentType,
                    $command['ContentType']
                );
            },
        ];
        $url = 'http://foo.s3.amazonaws.com/bar';

        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz']),
            new Result(['ETag' => 'A']),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
            new Result(['Location' => $url])
        ]);

        $uploader = new MultipartDownloader($client, $stream, $uploadOptions);
        $result = $uploader->download();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function testAppliesAmbiguousSuccessParsing()
    {
        $this->expectExceptionMessage("An exception occurred while downloading parts to a multipart download");
        $this->expectException(\Aws\S3\Exception\S3MultipartDownloadException::class);
        $counter = 0;

        $httpHandler = function ($request, array $options) use (&$counter) {
            if ($counter < 1) {
                $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><OperationNameResponse><UploadId>baz</UploadId></OperationNameResponse>";
            } else {
                $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n\n";
            }
            $counter++;

            return Promise\Create::promiseFor(
                new Psr7\Response(200, [], $body)
            );
        };

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'http_handler' => $httpHandler
        ]);

//        $data = str_repeat('.', 12 * 1048576);
//        $source = Psr7\Utils::streamFor($data);

        $filename = tmpfile();
        $dest = stream_get_meta_data($filename)['uri'];

        $downloader = new MultipartDownloader(
            $s3,
            $dest,
            [
                'bucket' => 'test-bucket',
                'key' => 'test-key',
                'checksum_validation_enabled' => false
            ]
        );
        $downloader->download();
    }
}


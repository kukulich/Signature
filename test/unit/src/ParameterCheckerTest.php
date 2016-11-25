<?php

declare(strict_types=1);

namespace SignatureTest;

use ReflectionClass;
use Signature\ParameterChecker;
use Signature\Encoder\EncoderInterface;
use Signature\Exception\SignatureException;
use Signature\Exception\SignatureDoesNotMatchException;
use Signature\Hasher\HasherInterface;
use SignatureTestFixture\ClassWithValidSignerProperty;

/**
 * @covers \Signature\ParameterChecker
 */
final class ParameterCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encoder;

    /**
     * @var HasherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $hasher;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->encoder = $this->createMock(EncoderInterface::class);
        $this->hasher  = $this->createMock(HasherInterface::class);
    }

    public function testInvalidSignatureException()
    {
        $checker = new ParameterChecker($this->encoder, $this->hasher);

        /* @var $reflection \ReflectionClass|\PHPUnit_Framework_MockObject_MockObject */
        $reflection = $this->createMock(ReflectionClass::class);

        $this->expectException(SignatureException::class);

        $checker->check($reflection, []);
    }

    public function testSignatureDoesNotMatchException()
    {
        $classFilePath = __DIR__ . '/../../fixture/UserClass.php';

        self::assertFileExists($classFilePath);

        $this->encoder->expects(self::once())->method('encode')->willReturn('123abc');

        $checker = new ParameterChecker($this->encoder, $this->hasher);

        /* @var $reflection \ReflectionClass|\PHPUnit_Framework_MockObject_MockObject */
        $reflection = $this->createMock(ReflectionClass::class);

        $reflection->expects(self::once())->method('getDefaultProperties')->willReturn(['verify' => '111']);

        $this->expectException(SignatureDoesNotMatchException::class);

        $checker->check($reflection, []);
    }

    public function testCheck()
    {
        $this->encoder->expects(self::exactly(1))->method('encode')->with([])->willReturn('YTowOnt9');
        $this->hasher->expects(self::exactly(1))->method('hash')->with([])->willReturn('40cd750bba9870f18aada2478b24840a');

        $checker = new ParameterChecker($this->encoder, $this->hasher);

        $checker->check(new ReflectionClass(ClassWithValidSignerProperty::class), []);
    }
}

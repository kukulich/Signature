<?php

declare(strict_types=1);

namespace SignatureTest;

use Signature\Signer;
use Signature\Encoder\Base64Encoder;
use Signature\Hasher\Md5Hasher;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Tests for {@see Signature\Signer}.
 *
 * @group   Unitary
 * @author  Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @covers \Signature\Signer
 */
final class SignerTest extends \PHPUnit_Framework_TestCase
{
    public function testItShouldSignAClass()
    {
        $signer         = new Signer(new Base64Encoder(), new Md5Hasher());
        $classGenerator = $signer->sign(new ClassGenerator('DummyClass'), ['foo']);
        $properties     = $classGenerator->getProperties();

        self::assertCount(1, $properties);
        self::assertArrayHasKey('verify4109c93b3462dad44dc7bc4215c9f174', $properties);
        self::assertInstanceOf(PropertyGenerator::class, $properties['verify4109c93b3462dad44dc7bc4215c9f174']);
    }
}

<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ProxyManagerTest\ProxyGenerator\Util;

use PHPUnit_Framework_TestCase;
use ProxyManager\ProxyGenerator\Util\Properties;
use ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ProxyManagerTestAsset\BaseClass;
use ProxyManagerTestAsset\ClassWithAbstractMagicMethods;
use ProxyManagerTestAsset\ClassWithAbstractProtectedMethod;
use ProxyManagerTestAsset\ClassWithAbstractPublicMethod;
use ProxyManagerTestAsset\ClassWithMagicMethods;
use ProxyManagerTestAsset\ClassWithMixedProperties;
use ProxyManagerTestAsset\EmptyClass;
use ProxyManagerTestAsset\HydratedObject;
use ProxyManagerTestAsset\LazyLoadingMock;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Tests for {@see \ProxyManager\ProxyGenerator\Util\Properties}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @covers \ProxyManager\ProxyGenerator\Util\Properties
 * @group Coverage
 */
class PropertiesTest extends PHPUnit_Framework_TestCase
{
    public function testGetPublicProperties()
    {
        $properties       = Properties::fromReflectionClass(new ReflectionClass(ClassWithMixedProperties::class));
        $publicProperties = $properties->getPublicProperties();

        $this->assertCount(3, $publicProperties);
        $this->assertInstanceOf(ReflectionProperty::class, $publicProperties['publicProperty0']);
        $this->assertInstanceOf(ReflectionProperty::class, $publicProperties['publicProperty1']);
        $this->assertInstanceOf(ReflectionProperty::class, $publicProperties['publicProperty2']);
    }

    public function testGetPublicPropertiesSkipsAbstractMethods()
    {
        $properties = Properties::fromReflectionClass(new ReflectionClass(ClassWithAbstractPublicMethod::class));

        $this->assertEmpty($properties->getPublicProperties());
    }

    public function testGetProtectedProperties()
    {
        $properties = Properties::fromReflectionClass(new ReflectionClass(ClassWithMixedProperties::class));

        $protectedProperties = $properties->getProtectedProperties();

        $this->assertCount(3, $protectedProperties);

        $this->assertInstanceOf(ReflectionProperty::class, $protectedProperties["\0*\0protectedProperty0"]);
        $this->assertInstanceOf(ReflectionProperty::class, $protectedProperties["\0*\0protectedProperty1"]);
        $this->assertInstanceOf(ReflectionProperty::class, $protectedProperties["\0*\0protectedProperty2"]);
    }

    public function testGetProtectedPropertiesSkipsAbstractMethods()
    {
        $properties = Properties::fromReflectionClass(new ReflectionClass(ClassWithAbstractProtectedMethod::class));

        $this->assertEmpty($properties->getProtectedProperties());
    }

    public function testGetPrivateProperties()
    {
        $properties = Properties::fromReflectionClass(new ReflectionClass(ClassWithMixedProperties::class));

        $privateProperties = $properties->getPrivateProperties();

        $this->assertCount(3, $privateProperties);

        $prefix = "\0" . ClassWithMixedProperties::class . "\0";

        $this->assertInstanceOf(ReflectionProperty::class, $privateProperties[$prefix . 'privateProperty0']);
        $this->assertInstanceOf(ReflectionProperty::class, $privateProperties[$prefix . 'privateProperty1']);
        $this->assertInstanceOf(ReflectionProperty::class, $privateProperties[$prefix . 'privateProperty2']);
    }
}

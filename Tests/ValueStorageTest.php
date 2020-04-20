<?php
/**
 * Copyright (c) 2020 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * ValueProviderTest.php
 * php-value-storage
 *
 * Created on 19.04.20 16:58 by thomas
 */

namespace TASoft\Util\Test;

use PHPUnit\Framework\TestCase;
use TASoft\Util\Test\Mock\MockValue;
use TASoft\Util\Test\Mock\MockValues;
use TASoft\Util\ValueStorage;

class ValueStorageTest extends TestCase
{
	public function testEmptyProvider() {
		$p = new ValueStorage();

		$this->assertCount(0, $p);
	}

	public function testScalarValues() {
		$p = new ValueStorage();
		$p->test = 23;
		$p["other-test"] = "Hello World";

		$this->assertCount(2, $p);
		$this->assertSame(23, $p["test"]);
		$this->assertSame("Hello World", $p->getValue("other-test"));

		$p["test"] = 44;
		$this->assertCount(2, $p);
		$this->assertSame(44, $p->test);
	}

	public function testValuesConstructor() {
		$p = new ValueStorage([
			'test' => 88,
			'other-test' => 'Hello World'
		]);

		$this->assertCount(2, $p);
		$this->assertSame(88, $p["test"]);
		$this->assertSame("Hello World", $p->getValue("other-test"));
	}

	public function testExistingValues() {
		$p = new ValueStorage([
			'test' => 88,
			'other-test' => 'Hello World'
		]);

		$this->assertFalse(isset($p["my-value"]));
		$this->assertTrue(isset($p["other-test"]));
	}

	public function testRemovingValues() {
		$p = new ValueStorage([
			'test' => 88,
			'other-test' => 'Hello World'
		]);

		$p->removeValue("test");
		$this->assertCount(1, $p);
		$this->assertNull($p["test"]);
		$this->assertSame("Hello World", $p->getValue("other-test"));

		unset($p["my-test"]);
		$this->assertCount(1, $p);

		unset($p["other-test"]);
		$this->assertCount(0, $p);
	}

	public function testCallableStorage() {
		$p = new ValueStorage();
		$func = function() { return 22; };

		$p->test = $func;
		$this->assertSame($func, $p->test);
		$this->assertSame(22, $p["test"]);

		$p = new ValueStorage([
			'test' => $func
		]);

		$this->assertSame($func, $p->test);
		$this->assertSame(22, $p["test"]);

		$p = new ValueStorage();
		$p["test"] = $func;

		$this->assertSame($func, $p->test);
		$this->assertSame(22, $p["test"]);
	}

	public function testValueObjectStorage() {
		$p = new ValueStorage();
		$value = new MockValue(22);

		$p->test = $value;
		$this->assertSame($value, $p->test);
		$this->assertSame(22, $p["test"]);

		$p = new ValueStorage([
			'test' => $value
		]);

		$this->assertSame($value, $p->test);
		$this->assertSame(22, $p["test"]);

		$p = new ValueStorage();
		$p["test"] = $value;

		$this->assertSame($value, $p->test);
		$this->assertSame(22, $p["test"]);
	}

	public function testValuesObjectStorage() {
		$p = new ValueStorage();
		$values = new MockValues();

		$values->values = [
			'test' => 22,
			'other-test' => 88
		];

		$p->test = $values;
		$this->assertSame(22, $p->test);
		$this->assertSame(88, $p["other-test"]);

		$p = new ValueStorage([
			$values
		]);

		$this->assertSame(22, $p->test);
		$this->assertSame(88, $p["other-test"]);

		$p = new ValueStorage();
		$p["test"] = $values;

		$this->assertSame(22, $p->test);
		$this->assertSame(88, $p["other-test"]);
	}
}

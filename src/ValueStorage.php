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

namespace TASoft\Util;


use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use TASoft\Util\Value\ValueInterface;
use TASoft\Util\Value\ValuesInterface;

class ValueStorage implements ArrayAccess, Countable, IteratorAggregate
{
	private $values = [];

	public function getIterator()
	{
		return new ArrayIterator($this->values);
	}

	/**
	 * ValueProvider constructor.
	 * @param array|iterable $values
	 */
	public function __construct(Iterable $values = [])
	{
		foreach($values as $key => $value)
			$this->addValue($key, $value);
	}

	/**
	 * @param string $key
	 * @param $value
	 * @return static
	 */
	public function addValue(string $key, $value) {
		if(NULL !== $value) {
			if($value instanceof ValuesInterface) {
				foreach($value->getValues() as $k => $v)
					$this->addValue($k, $v);
			} else
				$this->values[$key] = $value;
		}
		return $this;
	}

	/**
	 * @param string $key
	 */
	public function removeValue(string $key) {
		if(isset($this->values[$key]))
			unset($this->values[$key]);
	}

	/**
	 * Gets a value from storage
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	public function getValue(string $key) {
		$v = $this->values[$key] ?? NULL;

		if($v && is_callable($v))
			$v = call_user_func($v);

		if($v instanceof ValueInterface)
			$v = $v->getValue();

		return $v;
	}

	/**
	 * Gets the initial value from the storage
	 *
	 * @param string $key
	 */
	public function get(string $key) {
		return $this->values[$key] ?? NULL;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function hasValue(string $key): bool {
		return isset($this->values[$key]);
	}

	/**
	 * @inheritDoc
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * @inheritDoc
	 */
	public function __set($name, $value)
	{
		$this->addValue($name, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset)
	{
		return $this->hasValue($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset)
	{
		return $this->getValue($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value)
	{
		$this->addValue($offset, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset)
	{
		$this->removeValue($offset);
	}

	public function count()
	{
		return count($this->values);
	}
}
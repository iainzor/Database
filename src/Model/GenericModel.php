<?php
namespace Database\Model;

class GenericModel extends AbstractModel
{
	public function toBasicArray() { return $this->toArray(); }
}
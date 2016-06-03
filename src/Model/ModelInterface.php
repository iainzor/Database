<?php
namespace Database\Model;

interface ModelInterface
{
	public function toArray();
	
	public function toBasicArray();
}
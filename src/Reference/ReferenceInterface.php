<?php
namespace Database\Reference;

interface ReferenceInterface
{
	public function findAll(array $conditions);
}
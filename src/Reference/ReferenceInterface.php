<?php
namespace Database\Reference;

interface ReferenceInterface
{
	public function findAll(array $conditions);
	
	/**
	 * @return \Database\Table\Structure
	 */
	public function structure();
}
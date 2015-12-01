<?php
namespace Database\Model;

interface ModelGeneratorInterface
{
	/**
	 * @return AbstractModel
	 */
	public function createModelInstance();
	
	/**
	 * @return AbstractModel
	 */
	public function generateModel(array $properties);
}
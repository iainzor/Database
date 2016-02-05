<?php
namespace Database\Model;

trait ModelGeneratorTrait
{
	/**
	 * Generate a model for the table using a set of properties
	 * 
	 * @param array $properties
	 * @return AbstractModel
	 * @throws \Exception
	 */
	public function generateModel(array $properties)
	{
		if ($this instanceof ModelGeneratorInterface) {
			$model = $this->createModelInstance();
			if (!($model instanceof AbstractModel)) {
				throw new \Exception("Model must be an instance of \\Database\\Model\\AbstractModel");
			}
			AbstractModel::populate($model, $properties);

			return $model;
		} else {
			throw new \Exception("Could not generate model, class does not implement ModelGeneratorInterface");
		}
	}
}
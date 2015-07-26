<?php
namespace Database\Table;

interface StructureProviderInterface
{
	public function initStructure(Structure $structure);
}
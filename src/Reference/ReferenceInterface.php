<?php
namespace Database\Reference;

interface ReferenceInterface
{
	/**
	 * @return \Database\Query\SelectQuery
	 */
	public function selectQuery();
	
	/**
	 * @return \Database\Table\Structure
	 */
	public function structure();
}
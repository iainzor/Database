<?php
namespace Database\Driver;

use Database\Query\AbstractQuery;

interface SqlGeneratorInterface
{
	public function generate(AbstractQuery $query);
}
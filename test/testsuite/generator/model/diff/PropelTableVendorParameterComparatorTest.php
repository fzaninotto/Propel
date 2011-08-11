<?php

/*
 *	$Id: TableTest.php 1891 2010-08-09 15:03:18Z francois $
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/diff/PropelTableComparator.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/diff/PropelTableDiff.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/platform/MysqlPlatform.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/Database.php';


/**
 * Tests for the VendorParameter methods of the PropelTableComparator service class.
 *
 * @package    generator.model.diff
 */
class PropelTableVendorParameterComparatorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->platform = new MysqlPlatform();
	}

	public function testCompareSameVendorParameters()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$v1->setParameter('foo', 'bar');
		$v1->setParameter('baz', 'bazz');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$v2->setParameter('foo', 'bar');
		$v2->setParameter('baz', 'bazz');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$this->assertFalse(PropelTableComparator::computeDiff($t1, $t2));
	}

	public function testCompareNotSameVendorParameters()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$v1->setParameter('foo', '1');
		$v1->setParameter('bar', '2');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$v2->setParameter('baz', '3');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$diff = PropelTableComparator::computeDiff($t1, $t2);
		$this->assertTrue($diff instanceof PropelTableDiff);
	}

	public function testCaseInsensitive()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$v1->setParameter('foo', '1');
		$v1->setParameter('bar', '2');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$v2->setParameter('FoO', '1');
		$v2->setParameter('bAR', '2');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$this->assertFalse(PropelTableComparator::computeDiff($t1, $t2, true));
	}

	public function testCompareAddedVendorParameters()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$v2->setParameter('foo', 'bar');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$tc = new PropelTableComparator();
		$tc->setFromTable($t1);
		$tc->setToTable($t2);
		$nbDiffs = $tc->compareVendorParameters();
		$tableDiff = $tc->getTableDiff();
		$this->assertEquals(1, $nbDiffs);
		$this->assertEquals(1, count($tableDiff->getAddedVendorParameters()));
		$this->assertEquals(array('foo' => 'bar'), $tableDiff->getAddedVendorParameters());
	}

	public function testCompareAddedVendorParametersFromEmpty()
	{
		$t1 = new Table('Baz');
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$v2->setParameter('foo', 'bar');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$tc = new PropelTableComparator();
		$tc->setFromTable($t1);
		$tc->setToTable($t2);
		$nbDiffs = $tc->compareVendorParameters();
		$tableDiff = $tc->getTableDiff();
		$this->assertEquals(1, $nbDiffs);
		$this->assertEquals(1, count($tableDiff->getAddedVendorParameters()));
		$this->assertEquals(array('foo' => 'bar'), $tableDiff->getAddedVendorParameters());
	}

	public function testCompareRemovedVendorParameters()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$v1->setParameter('foo', 'bar');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$tc = new PropelTableComparator();
		$tc->setFromTable($t1);
		$tc->setToTable($t2);
		$nbDiffs = $tc->compareVendorParameters();
		$tableDiff = $tc->getTableDiff();
		$this->assertEquals(1, $nbDiffs);
		$this->assertEquals(1, count($tableDiff->getRemovedVendorParameters()));
		$this->assertEquals(array('foo' => 'bar'), $tableDiff->getRemovedVendorParameters());
	}

	public function testCompareRemovedVendorParametersToEmpty()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$v1->setParameter('foo', 'bar');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$t2 = new Table('Baz');

		$tc = new PropelTableComparator();
		$tc->setFromTable($t1);
		$tc->setToTable($t2);
		$nbDiffs = $tc->compareVendorParameters();
		$tableDiff = $tc->getTableDiff();
		$this->assertEquals(1, $nbDiffs);
		$this->assertEquals(1, count($tableDiff->getRemovedVendorParameters()));
		$this->assertEquals(array('foo' => 'bar'), $tableDiff->getRemovedVendorParameters());
	}
	
	public function testCompareModifiedVendorParameters()
	{
		$v1 = new VendorInfo();
		$v1->setType('mysql');
		$v1->setParameter('foo', 'bar');
		$t1 = new Table('Baz');
		$t1->addVendorInfo($v1);
		$v2 = new VendorInfo();
		$v2->setType('mysql');
		$v2->setParameter('foo', 'baz');
		$t2 = new Table('Baz');
		$t2->addVendorInfo($v2);

		$tc = new PropelTableComparator();
		$tc->setFromTable($t1);
		$tc->setToTable($t2);
		$nbDiffs = $tc->compareVendorParameters();
		$tableDiff = $tc->getTableDiff();
		$this->assertEquals(1, $nbDiffs);
		$this->assertEquals(1, count($tableDiff->getModifiedVendorParameters()));
		$this->assertEquals(array('foo' => array('bar', 'baz')), $tableDiff->getModifiedVendorParameters());
	}

}

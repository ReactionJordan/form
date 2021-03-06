<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Form\Tests;

use Joomla\Test\TestHelper;
use Joomla\Form\Field\FileListField;
use SimpleXmlElement;

/**
 * Test class for JFormFieldFileList.
 *
 * @coversDefaultClass Joomla\Form\Field\FileListField
 * @since  1.0
 */
class JFormFieldFileListTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test data for getGroups test
	 *
	 * @return  array
	 *
	 * @since __VERSION_NO__
	 */
	public function dataGetOptions()
	{
		return array(
			array(
				array(),
				array(
					(object) array('value' => '-1', 'text' => 'JOPTION_DO_NOT_USE', 'disable' => false),
					(object) array('value' => '', 'text' => 'JOPTION_USE_DEFAULT', 'disable' => false),
					(object) array('value' => 'JFormField.xml', 'text' => 'JFormField.xml', 'disable' => false),
				)
			),
			array(
				array('hide_none' => 'true'),
				array(
					(object) array('value' => '', 'text' => 'JOPTION_USE_DEFAULT', 'disable' => false),
					(object) array('value' => 'JFormField.xml', 'text' => 'JFormField.xml', 'disable' => false),
				)
			),
			array(
				array('hide_default' => 'true'),
				array(
					(object) array('value' => '-1', 'text' => 'JOPTION_DO_NOT_USE', 'disable' => false),
					(object) array('value' => 'JFormField.xml', 'text' => 'JFormField.xml', 'disable' => false),
				)
			),
			array(
				array(
					'hide_default' => 'true',
					'hide_none' => 'true',
					'stripext' => 'true',
					'exclude' => 'foobar'
				),
				array(
					(object) array('value' => 'JFormField', 'text' => 'JFormField', 'disable' => false),
				)
			),
			array(
				array('exclude' => 'JFormField.xml'),
				array(
					(object) array('value' => '-1', 'text' => 'JOPTION_DO_NOT_USE', 'disable' => false),
					(object) array('value' => '', 'text' => 'JOPTION_USE_DEFAULT', 'disable' => false),
				)
			),
		);
	}

	/**
	 * Test the getInput method.
	 *
	 * @param   array  $inputs    Inputs to set the state
	 * @param   array  $expected  Expected file list
	 *
	 * @return  void
	 *
	 * @covers        ::getOptions
	 * @dataProvider  dataGetOptions
	 * @since         1.0
	 */
	public function testGetOptions($inputs, $expected)
	{
		$xml = '<field name="filelist" type="filelist"';
		$inputs['directory'] = __DIR__ . "/testfiles";

		foreach ($inputs as $attr => $value)
		{
			$xml .= " $attr=\"$value\"";
		}

		$xml .= ' />';

		$field = new FileListField;

		$xml = new SimpleXmlElement($xml);

		$this->assertTrue(
			$field->setup($xml, 'setupValue'),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$options = TestHelper::invoke($field, 'getOptions');

		$this->assertEquals($expected, $options);
	}
}

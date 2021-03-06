<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Form\Tests;

use Joomla\Test\TestHelper;
use Joomla\Form\Field\GroupedListField;
use SimpleXmlElement;

/**
 * Test class for JFormFieldGroupedList.
 *
 * @coversDefaultClass Joomla\Form\Field\GroupedListField
 * @since  1.0
 */
class JFormFieldGroupedListTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test data for getGroups test
	 *
	 * @return  array
	 *
	 * @since __VERSION_NO__
	 */
	public function dataGetInput()
	{
		return array(
			'basic' => array(
				'inputs' => array(
					'id' => 'myId',
					'name' => 'myName',
				),
				'expected' => array(
					'tag' => 'select',
					'attributes' => array(
						'id' => 'myId',
						'name' => 'myName',
					),
					'child' => array(
						'tag' => 'optgroup',
						'attributes' => array(
							'label' => 'barfoo',
						)
					)
				),
			),
			'allAttrSet' => array(
				'inputs' => array(
					'id' => 'myId',
					'name' => 'myName',
					'class' => 'aClass',
					'disabled' => 'true',
					'size' => '50',
					'multiple' => 'true',
				),
				'expected' => array(
					'tag' => 'select',
					'attributes' => array(
						'id' => 'myId',
						'name' => 'myName[]',
						'class' => 'aClass',
						'disabled' => 'disabled',
						'size' => '50',
						'multiple' => 'multiple',
					)
				),
			),
			'readonlySelectNoName' => array(
				'inputs' => array(
					'id' => 'myId',
					'name' => 'myName',
					'readonly' => 'true'
				),
				'expected' => array(
					'tag' => 'select',
					'attributes' => array(
						'id' => 'myId',
						'name' => '',
					)
				),
			),
			'readonlyHiddenInput' => array(
				'inputs' => array(
					'id' => 'myId',
					'name' => 'myName',
					'readonly' => 'true'
				),
				'expected' => array(
					'tag' => 'input',
					'attributes' => array(
						'type' => 'hidden',
						'name' => 'myName',
						'value' => 'setupValue',
					)
				),
			),
		);
	}

	/**
	 * Test the getInput method.
	 *
	 * @param   array  $inputs    Inputs to set the state
	 * @param   array  $expected  Expected Output tags
	 *
	 * @return  void
	 *
	 * @covers        ::getInput
	 * @dataProvider  dataGetInput
	 * @since         1.0
	 */
	public function testGetInput($inputs, $expected)
	{
		$xml = '<field type="groupedlist"';

		foreach ($inputs as $attr => $value)
		{
			$xml .= " $attr=\"$value\"";
		}

		$xml .= '/>';

		$field = $this->getMock('Joomla\\Form\\Field\\GroupedListField', array('getGroups'));

		// Configure the stub.
		$field->expects($this->any())
			->method('getGroups')
			->will(
				$this->returnValue(
					array(
					'barfoo' => array(
							(object) array('value' => 'oof', 'text' => 'Foo')
						)
					)
				)
			);

		$xml = new SimpleXmlElement($xml);

		$this->assertTrue(
			$field->setup($xml, 'setupValue'),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$this->assertTag(
			$expected,
			$field->input,
			'Line:' . __LINE__ . ' The getInput method should compute and return attributes correctly.'
		);
	}

	/**
	 * Test data for getGroups test
	 *
	 * @return  array
	 *
	 * @since __VERSION_NO__
	 */
	public function dataGetGroups()
	{
		return array(
			array('<option value="oof" disabled="true">Foo</option>'
				. '<option value="rab" class="lorem">Bar</option>',
				array(
					0 => array(
						(object) array(
							'value' => 'oof',
							'text' => 'Foo',
							'disable' => true,
							'class' => '',
							'onclick' => ''
						),
						(object) array(
							'value' => 'rab',
							'text' => 'Bar',
							'disable' => false,
							'class' => 'lorem',
							'onclick' => ''
						),
					),
				),
			),
			array('<group label="barfoo"><option value="oof" disabled="true">Foo</option>'
				. '<option value="rab" class="lorem">Bar</option></group>',
				array(
					'barfoo' => array(
						(object) array(
							'value' => 'oof',
							'text' => 'Foo',
							'disable' => true,
							'class' => '',
							'onclick' => ''
						),
						(object) array(
							'value' => 'rab',
							'text' => 'Bar',
							'disable' => false,
							'class' => 'lorem',
							'onclick' => ''
						),
					),
				),
			),
			array('<option value="foo">Foo</option>'
				. '<group label="barfoo"><option value="oof" disabled="true">Foo</option>'
				. '<foo>bar</foo>'
				. '<option value="rab" class="lorem">Bar</option></group>'
				. '<option value="bar">Bar</option>',
				array(
					0 => array(
						(object) array(
							'value' => 'foo',
							'text' => 'Foo',
							'disable' => false,
							'class' => '',
							'onclick' => ''
						),
					),
					'barfoo' => array(
						(object) array(
							'value' => 'oof',
							'text' => 'Foo',
							'disable' => true,
							'class' => '',
							'onclick' => ''
						),
						(object) array(
							'value' => 'rab',
							'text' => 'Bar',
							'disable' => false,
							'class' => 'lorem',
							'onclick' => ''
						),
					),
					2 => array(
						(object) array(
							'value' => 'bar',
							'text' => 'Bar',
							'disable' => false,
							'class' => '',
							'onclick' => ''
						),
					),
				),
			),
		);
	}

	/**
	 * Test the getGroups method.
	 *
	 * @param   string  $optionTag  @todo
	 * @param   string  $expected   @todo
	 *
	 * @return  void
	 *
	 * @covers ::getGroups
	 * @dataProvider dataGetGroups
	 * @since   1.0
	 */
	public function testGetGroups($optionTag, $expected)
	{
		$field = new GroupedListField;

		$fieldStartTag = '<field name="myName" type="groupedlist">';
		$fieldEndTag = '</field>';

		$xml = new SimpleXmlElement($fieldStartTag . $optionTag . $fieldEndTag);
		$this->assertTrue(
			$field->setup($xml, 'value'),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$groups = TestHelper::invoke($field, 'getGroups');

		foreach ($expected as $i => $expectedGroup)
		{
			$this->assertTrue(
				in_array($expectedGroup, $groups),
				'Line:' . __LINE__ . ' The getGroups method should compute group #'
				. $i . ' correctly'
			);
		}
	}

	/**
	 * Test the getGroups method.
	 *
	 * @return  void
	 *
	 * @covers             ::getGroups
	 * @expectedException  UnexpectedValueException
	 * @since              1.0
	 */
	public function testGetGroupsUnknownChildException()
	{
		$field = new GroupedListField;

		$fieldStartTag = '<field name="myName" type="groupedlist">';
		$optionTag = '<item value="foo">Bar</item>';
		$fieldEndTag = '</field>';

		$xml = new SimpleXmlElement($fieldStartTag . $optionTag . $fieldEndTag);
		$this->assertTrue(
			$field->setup($xml, 'value'),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$groups = TestHelper::invoke($field, 'getGroups');
	}
}

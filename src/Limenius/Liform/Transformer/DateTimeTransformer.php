<?php

/*
 * This file is part of the Limenius\Liform package.
 *
 * (c) Limenius <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Limenius\Liform\Transformer;

use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class DateTimeTransformer extends AbstractTransformer
{
	/**
	 * {@inheritdoc}
	 */
	public function transform(FormInterface $form, array $extensions = [], $widget = null)
	{
		$schema = ['type' => 'datetime'];
		$schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);

		return $schema;
	}
}

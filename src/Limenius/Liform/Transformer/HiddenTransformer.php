<?php

namespace Limenius\Liform\Transformer;

use Symfony\Component\Form\FormInterface;

class HiddenTransformer extends AbstractTransformer
{
	/**
	 * Given that this transformation will only be called when the type is a hidden, it allows us to define how to build
	 * the schema.
	 *
	 * {@inheritdoc}
	 */
	public function transform(FormInterface $form, array $extensions = [], $widget = null)
	{
		$schema = ['type' => 'string'];
		$schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);

		return $schema;
	}
}

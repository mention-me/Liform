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
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
abstract class AbstractTransformer implements TransformerInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormTypeGuesserInterface|null
     */
    protected $validatorGuesser;

    /**
     * @param TranslatorInterface           $translator
     * @param FormTypeGuesserInterface|null $validatorGuesser
     */
    public function __construct(TranslatorInterface $translator, FormTypeGuesserInterface $validatorGuesser = null)
    {
        $this->translator = $translator;
        $this->validatorGuesser = $validatorGuesser;
    }

    /**
     * @param ExtensionInterface[] $extensions
     * @param FormInterface        $form
     * @param array                $schema
     *
     * @return array
     */
    protected function applyExtensions(array $extensions, FormInterface $form, array $schema)
    {
        $newSchema = $schema;
        foreach ($extensions as $extension) {
            $newSchema = $extension->apply($form, $newSchema);
        }

        return $newSchema;
    }

    /**
     * @param FormInterface        $form
     * @param array                $schema
     * @param ExtensionInterface[] $extensions
     * @param string               $widget
     *
     * @return array
     */
    protected function addCommonSpecs(FormInterface $form, array $schema, $extensions = [], $widget)
    {
        $schema = $this->addLabel($form, $schema);
        $schema = $this->addPlaceholder($form, $schema);
        $schema = $this->addAttr($form, $schema);
        $schema = $this->addPattern($form, $schema);
        $schema = $this->addDescription($form, $schema);
        $schema = $this->addWidget($form, $schema, $widget);
        $schema = $this->applyExtensions($extensions, $form, $schema);

        return $schema;
    }

    public function addPlaceholder(FormInterface $form, array $schema)
    {
        if ($attr = $form->getConfig()->getOption('placeholder')) {
            $schema['placeholder'] = $attr;
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addPattern(FormInterface $form, array $schema)
    {
        if ($attr = $form->getConfig()->getOption('attr')) {
            if (isset($attr['pattern'])) {
                $schema['pattern'] = $attr['pattern'];
            }
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addLabel(FormInterface $form, array $schema)
    {
        $translationDomain = $form->getConfig()->getOption('translation_domain');
        if ($label = $form->getConfig()->getOption('label')) {
            $schema['title'] = $this->translator->trans($label, [], $translationDomain);
        } else {
            $schema['title'] = $this->translator->trans($form->getName(), [], $translationDomain);
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addAttr(FormInterface $form, array $schema)
    {
        if ($attr = $form->getConfig()->getOption('attr')) {
            $schema['attr'] = $attr;
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addDescription(FormInterface $form, array $schema)
    {
        $translationDomain = $form->getConfig()->getOption('translation_domain');

        $formConfig = $form->getConfig();

        /*
         * Here at Mention Me we use the "help_block" from the MopaBootstrapBundle.
         * As part of Symfony 4.1, the "help" option has been standardised. We should prepare to switch over to
         * that.
         *
         * In the meantime, we'll look at both: the help field first, and if not set, the help_block.
         */
        $helpFields = ["help", "help_block"];
        foreach ($helpFields as $helpField) {
            if ($help = $formConfig->getOption($helpField, '')) {
                $schema['description'] = $this->translator->trans($help, [], $translationDomain);
            }
        }

        if ($liform = $formConfig->getOption('liform')) {
            if (isset($liform['description']) && $description = $liform['description']) {
                $schema['description'] = $this->translator->trans($description, [], $translationDomain);
            }
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     * @param mixed         $configWidget
     *
     * @return array
     */
    protected function addWidget(FormInterface $form, array $schema, $configWidget)
    {
        if ($liform = $form->getConfig()->getOption('liform')) {
            if (isset($liform['widget']) && $widget = $liform['widget']) {
                $schema['widget'] = $widget;
            }
        } elseif ($configWidget) {
            $schema['widget'] = $configWidget;
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     *
     * @return boolean
     */
    protected function isRequired(FormInterface $form)
    {
        return $form->getConfig()->getOption('required');
    }

        /**
     * @param FormInterface $form
     *
     * @return boolean
     */
    protected function isDisabled(FormInterface $form)
    {
        return $form->getConfig()->getOption('disabled');
    }
}

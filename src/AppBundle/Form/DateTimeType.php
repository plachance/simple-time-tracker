<?php

declare(strict_types=1);

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType as SymfonyDateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DateTimeType.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class DateTimeType extends SymfonyDateTimeType
{
	const HTML5_FORMAT = "yyyy-MM-dd'T'HH:mm:ss";

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['widget'] = $options['widget'];

		// Change the input to a HTML5 datetime input if
		//  * the widget is set to "single_text"
		//  * the format matches the one expected by HTML5
		//  * the html5 is set to true
		if($options['html5'] && 'single_text' === $options['widget'] && self::HTML5_FORMAT === $options['format'])
		{
			$view->vars['type'] = 'datetime-local';
		}
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		parent::configureOptions($resolver);
		$resolver->setDefault('format', self::HTML5_FORMAT);
	}
}

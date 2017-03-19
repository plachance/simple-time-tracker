<?php

declare(strict_types=1);

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProjectType.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class ProjectType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('no', null,
				[
				'label' => 'No:',
				'disabled' => $options['noDisabled'],
			])
			->add('description', null, ['label' => 'Description:'])
			->add('color', null, [
				'label' => 'Color:',
				'attr' => [
					'class' => 'colorpicker',
					'placeholder' => 'Color (E.g.: #47c3d3)',
				],
				])
			->add('pinned', null, [
				'required' => false,
			])
		;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Project',
			'noDisabled' => false,
		]);
	}
}

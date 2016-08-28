<?php

declare(strict_types=1);

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PreferencesType.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class PreferencesType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('dayLength', TextType::class, [
				'label' => 'Workday length (in hours):',
				'attr' => [
					'min' => '0.01',
					'max' => '23.99',
					'step' => '0.01',
				],
				])
			->add('submit', SubmitType::class,
				[
				'label' => 'Save',
				'attr' => [
					'class' => 'btn btn-primary',
				],
			])
		;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\User',
		]);
	}

}

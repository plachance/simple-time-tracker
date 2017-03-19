<?php

declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Repository\ProjectRepository;
use AppBundle\Util\DateTimeSecondsFixViewTransformer;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TaskType.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class TaskType extends AbstractType
{
	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$dateTimeFixTransformer = new DateTimeSecondsFixViewTransformer();
		$builder->add('project', null,
				[
				'label' => 'Project:',
				'placeholder' => 'Select a value',
				'attr' => [
					'data-placeholder' => $this->translator->trans('Select a value'),
				],
				'query_builder' => function (ProjectRepository $repo) use ($options)
			{
				return $repo->createQueryBuilder('p')
					->innerJoin('p.user', 'u')
					->where('u.id = :userId')
					->orderBy('p.no', 'desc')
					->addOrderBy('p.description', 'desc')
					->setParameter('userId', $options['user']);
			},
			])
			->add('dateTimeBegin', DateTimeType::class,
				[
				'widget' => 'single_text',
				'attr' => [
					'step' => '1',
				],
				'label' => 'Begin:',
				'required' => false,
				'empty_data' => (new DateTime())->format(DateTime::W3C),
			])
			->add('dateTimeEnd', DateTimeType::class,
				[
				'widget' => 'single_text',
				'attr' => [
					'step' => '1',
				],
				'label' => 'End:',
				'required' => false,
		]);
		$builder->get('dateTimeBegin')->addViewTransformer($dateTimeFixTransformer);
		$builder->get('dateTimeEnd')->addViewTransformer($dateTimeFixTransformer);
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Task',
			'user' => null,
		]);
	}
}

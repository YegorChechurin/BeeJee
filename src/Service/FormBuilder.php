<?php

namespace App\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormBuilder
{
	public static function buildTaskCreationForm(FormFactoryInterface $formFactory)
	{
		return $formFactory->createBuilder()
		           ->add('username', TextType::class)
		           ->add('email', TextType::class)
		           ->add('text', TextType::class)
		           ->add('save', SubmitType::class, ['label' => 'Create Task'])
		           ->getForm();
	}
}
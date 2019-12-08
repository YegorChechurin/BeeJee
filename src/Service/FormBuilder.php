<?php

namespace App\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class FormBuilder
{
	public static function buildTaskCreationForm(FormFactoryInterface $formFactory)
	{
		return $formFactory->createBuilder()
		           ->add('username', TextType::class, [
		           	   'constraints' => new NotBlank(),
		           ])
		           ->add('email', TextType::class, [
		           	   'constraints' => [
		           	   	   new NotBlank(),
		           	   	   new Email(),
		           	   ]
		           ])
		           ->add('text', TextType::class, [
		           	   'constraints' => new NotBlank(),
		           ])
		           ->add('save', SubmitType::class, ['label' => 'Create Task'])
		           ->getForm();
	}
}
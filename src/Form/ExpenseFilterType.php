<?php

namespace App\Form;

use App\Controller\ExpenseController;
use App\Entity\Category;
use App\Entity\Expense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ExpenseFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('priceMin', NumberType::class, [
                'required' => false,
            ])
            ->add('priceMax', NumberType::class, [
                'required' => false,
            ])
            ->add('createdAt', DateType::class, [
                'required' => false,
            ])
            ->add('sortItem', ChoiceType::class, [
                'choices' => array_map(null, ExpenseController::EXPENSE_SORT_FIELDS),
            ])
            ->add('sortType', ChoiceType::class, [
                'choices' => [
                    'DESC' => 'DESC',
                    'ASC' => 'ASC',

                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use phpDocumentor\Reflection\Types\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Produits')
            ->setEntityLabelInSingular('Produit')

            ->setPageTitle("index", "PauseCafé - Administration des produits")
            ->setPaginatorPageSize(15);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description'),
            MoneyField::new('price')->setCurrency('EUR'),
            IntegerField::new('stock'),
            DateTimeField::new('createdAt')
                ->hideOnForm()
                ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('categories')
                ->setCrudController(CategoriesCrudController::class),
            SlugField::new('slug')
                ->setTargetFieldName('name'),
            // ->hideOnForm(),
            // ->setFormTypeOption('disabled', 'disabled'),
            // ->setFormTypeOption('disabled', 'disabled'),
            AssociationField::new('images')
                ->setFormTypeOption('mapped', false)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('disabled', 'disabled')
                ->setCrudController(ImagesCrudController::class),
            // ImageField::new('images')->setUploadDir('assets/images/products/')
            // ->setFormTypeOption('by_reference', false)
            // ->setFormTypeOption('allow_add', true)
            // ->setFormTypeOption('allow_delete', true)
            // ->setFormTypeOption('entry_type', ImagesCrudController::class)
            // ->setFormTypeOption('entry_options', [
            //     'label' => false,
            //     'required' => false,
            // ])
            // ->setFormTypeOption('disabled', 'disabled'),

            // CollectionField::new('images')
            //     ->setEntryIsComplex(true)
            //     ->setTemplatePath('public/images/products/')
            //     ->setFormTypeOption('by_reference', false)
            //     ->setFormTypeOption('allow_add', true)
            //     ->setFormTypeOption('allow_delete', true)
            //     ->setFormTypeOption('entry_type', ImagesCrudController::class)
            //     ->setFormTypeOption('entry_options', [
            //         'label' => false,
            //         'required' => false,
            //     ])
            //     ->setFormTypeOption('mapped', false),
            // CollectionField::new('ordersDetails')
        ];
    }

}

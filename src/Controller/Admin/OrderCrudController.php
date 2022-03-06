<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    //Triage des ID descendant
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC']);
    }
    
    //Detail de la commande
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Commandé le'),
            TextField::new('user.getfullname', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison'),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3,
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }

    //Boutons mise à jour du statut de la livraison
    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    //Fonction modification du state à 2 et envoi du mail
    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        //Message flash information livraison
        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande ".$order->getReference()." est <u>en cours de préparation</u></strong></span>");

        //Mail de préparation de la commande
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstName()." ".$order->getUser()->getLastName()."
        <br><br> Votre commande n°".$order->getReference()." est actuellement en cours de préparation.
        <br><br> Vous serez informé de l'expédition de votre commande par email.
        <br><br> Vous pouvez retrouver le détail de votre commande dans votre compte, onglet 'Mes commandes'.
        <br><br> A très bientôt sur Vincent LHMTE Art. ";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Préparation de votre commande en cours -  Vincent LHMTE Art.', $content);

        $routeBuilder = $this->get(AdminUrlGenerator::class);
 
        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->setAction('index')->generateUrl());
    }

    //Fonction modification du state à 3 et envoi du mail
    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande ".$order->getReference()." est <u>en cours de livraison</u></strong></span>");
        
        //Mail d'expédition de la commande
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstName()." ".$order->getUser()->getLastName()."
        <br><br> Bonne nouvelle : votre commande n°".$order->getReference()." vient d’être expédiée !
        <br><br> J'espère de tout cœur que votre commande vous plaira.
        <br><br> Vous pouvez retrouver le détail de votre commande dans votre compte, onglet 'Mes commandes'.
        <br><br> A très bientôt sur Vincent LHMTE Art. ";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), "Confirmation d'expédition de votre commande -  Vincent LHMTE Art.", $content);
        $routeBuilder = $this->get(AdminUrlGenerator::class);
 
        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->setAction('index')->generateUrl());
    }
    
}

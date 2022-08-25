<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeInformation;
use App\Entity\Livraison;
use App\Entity\Panier;
use App\Form\CommandeFormType;
use App\Form\LivraisonFormType;
use App\Form\PanierFormType;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PanierController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(SessionInterface  $session,
                          ProduitRepository $produitRepository,
                          ManagerRegistry   $managerRegistry): Response
    {

        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $totale = 0;
        foreach ($panierWithData as $item) {
            $totaleItem = $item['product']->getPrice() * $item['quantity'];
            $totale += $totaleItem;

        }

        $alltotal = $totale+45;
        $livraison = new Livraison();
        $livraisonForm = $this->createForm(LivraisonFormType::class, $livraison);
        return $this->render('checkout/index.html.twig', [
            'items' => $panierWithData,
            'totale' => $totale,
            'alltotal'=>$alltotal,
            'livraisonForm' => $livraisonForm->createView()
        ]);
    }

    /**
     * @Route("/submitChkout", name="submitChkout")
     */
    public function submitCommande(Request           $request,
                                   SessionInterface  $session,
                                   ProduitRepository $produitRepository,
                                   ManagerRegistry   $managerRegistry,
                                                     $stripeSK)
    {

        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $totale = 0;
        foreach ($panierWithData as $item) {
            $totaleItem = $item['product']->getPrice() * $item['quantity'];
            $totale += $totaleItem;
        }

        $livraison = new Livraison();
        $livraisonForm = $this->createForm(LivraisonFormType::class, $livraison);
        $livraisonForm->handleRequest($request);
        if ($livraisonForm->isSubmitted()) {


            $manager = $managerRegistry->getManager();

            $panier = new Panier();
            $panier->setDateCreation(new \DateTime());
            $panier->setTotale($totale);
            $panier->setUser($this->getUser());

            $commande = new Commande();
            $commande->setUser($this->getUser());
            $commande->setLivraison($livraison);
            $commande->setEtat("En_cour");
            $commande->setTotale($totale);
            $commande->setDateCreation(new \DateTime());

            $commandeInfos = array();
            foreach ($panierWithData as $item) {
                $commandeInfo = new CommandeInformation();
                $commandeInfo->setProduct($item['product']);
                $commandeInfo->setQuantity($item['quantity']);
                $commandeInfo->setCommande($commande);

                array_push($commandeInfos, $commandeInfo);

                $commande->addCommandeInformation($commandeInfo);
                $manager->persist($commandeInfo);
            }

            $livraison->setCommande($commande);
            $livraison->setPrix($totale);

            \Stripe\Stripe::setApiKey($stripeSK);

            //Prepare data for paiement
            //$line_items == paiement en ligne
            $line_items = [];
            $price_data = [];
            foreach ($panierWithData as $item) {
                $price_data = array(
                    'currency' => 'usd',
                    "unit_amount" => $item['product']->getPrice() * 100,
                    "product_data" => array(
                        'name' => $item['product']->getName()
                    )
                );
                array_push($line_items, array(
                    'price_data' => $price_data,
                    'quantity' => $item['quantity']
                ));
            }


            //session de paiement
            $paiementSession = Session::create([
                'line_items' => [$line_items],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);


            $manager->persist($livraison);
            $manager->persist($panier);
            $manager->flush();
            $session->remove('panier');
            return $this->redirect($paiementSession->url, 303);
        }


        return $this->render('checkout/index.html.twig', [
            'items' => $panierWithData,
            'totale' => $totale,
            'livraisonForm' => $livraisonForm->createView()
        ]);

    }


    /**
     * @Route("/success-url", name="success_url")
     */
    public function successUrl(): Response
    {
        return $this->render('payment/success.html.twig', []);
    }

    /**
     * @Route("/cancel-ur", name="cancel_url")
     */
    public function canceUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }

}

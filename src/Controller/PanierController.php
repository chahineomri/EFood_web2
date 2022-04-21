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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
        $livraison = new Livraison();
        $livraisonForm = $this->createForm(LivraisonFormType::class, $livraison);
        return $this->render('checkout/index.html.twig', [
            'items' => $panierWithData,
            'totale' => $totale,
            'livraisonForm' => $livraisonForm->createView()
        ]);
    }

    /**
     * @Route("/submitChkout", name="submitChkout")
     */
    public function submitCommande(Request             $request,
                                   SessionInterface  $session,
                                   ProduitRepository $produitRepository,
                                   ManagerRegistry   $managerRegistry)
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
            $panier->setEtat("En_Cour");
            $panier->setTotale($totale);
            $panier->setUser($this->getUser());

            $commande = new Commande();
            $commande->setUser($this->getUser());
            $commande->setLivraison($livraison);

            $commandeInfos = array();
            foreach ($panierWithData as $item) {
                $commandeInfo = new CommandeInformation();
                $commandeInfo->setProduct($item['product']);
                $commandeInfo->setQuantity($item['quantity']);
                $commandeInfo->setCommande($commande);
                array_push($commandeInfos, $commandeInfo);
                $manager->persist($commandeInfo);
            }

            $livraison->setCommande($commande);
            $livraison->setPrix($totale);

            $manager->persist($livraison);
            $manager->persist($panier);
            $manager->flush();
        }


        return $this->render('checkout/index.html.twig', [
            'items' => $panierWithData,
            'totale' => $totale,
            'livraisonForm' => $livraisonForm->createView()
        ]);

    }
}

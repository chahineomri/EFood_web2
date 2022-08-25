<?php

namespace App\Controller;

use App\Data\pdfService;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(SessionInterface $session, ProduitRepository $produitRepository)
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
        //dd($panierWithData);
        foreach ($panierWithData as $item) {
            $totaleItem = $item['product']->getPrice() * $item['quantity'];
            $totale += $totaleItem;
        }

        $alltotal = $totale + 45;
        return $this->render('commande/index.html.twig', [
            'items' => $panierWithData,
            'totale' => $totale,
            'alltotal' => $alltotal,
        ]);
    }

    /**
     * @Route("panier/add/{id}",name="panier_add" )
     */
    public function add($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;

        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("panier/remove/{id}", name="panier_remove")
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/order-history", name="mesCommandes")
     */
    public function findCommndByUser(CommandeRepository $commandeRepository)
    {
        $userid = $this->getUser()->getId();
        $commandes = $commandeRepository->findCommandeByUser($userid);
        return $this->render('commande/historiques.html.twig', [
            'commandes' => $commandes
        ]);
    }


    /**
     * @Route("/facture/{id}", name="facture")
     */
    public function downloadFacture(pdfService $pdfService, Request $request,CommandeRepository $commandeRepository,$id){
        /*$html = json_decode($request->getContent(), true);
        $pdfService->showPdfFile($html['html']);
        return $html['html'];*/

        $commandes= $commandeRepository->findBy(array('id'=>$id));
        $commande = $commandes[0];
        $dueDate = $commande->getDateCreation()->modify('+5 day');
        $html =  $this->render('commande/facture.html.twig', [
            'commande' => $commande,
            'dueDate' => $dueDate
        ]);
        $pdfService->showPdfFile($html);
    }

    /**
     * @Route("/order-history/{id}", name="singleCommande")
     */
    public function findCommndById(CommandeRepository $commandeRepository,$id)
    {

        $commandes= $commandeRepository->findBy(array('id'=>$id));
        //dd($commandes->getCommandeInformations());

        return $this->render('commande/commandeDetails.html.twig', [
            'commandes' => $commandes
        ]);
    }

}

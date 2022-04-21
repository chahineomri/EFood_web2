<?php

namespace App\Controller;

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
        return $this->render('commande/index.html.twig', [
            'items' => $panierWithData,
            'totale' => $totale
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
    public function remove($id, SessionInterface $session){
        $panier = $session->get('panier',[]);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

}

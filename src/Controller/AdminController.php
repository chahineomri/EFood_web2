<?php

namespace App\Controller;

use App\Entity\CommandeInformation;
use App\Repository\CommandeInformationRepository;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/getAllCommands", name="get_commands")
     */
    public function getAllCommands(CommandeRepository $commandeRepository)
    {
        $commandes = $commandeRepository->findAll();
        $data = [];
        foreach ($commandes as $commande) {
            $userFullName = $commande->getUser()->getUsername();
            $commandeId = $commande->getId();
            $etatCommande = $commande->getEtat();
            $totale = $commande->getTotale();
            foreach ($commande->getCommandeInformations() as $commandeInfo) {
                $productname = $commandeInfo->getProduct()->getName();
                $productprice = $commandeInfo->getProduct()->getPrice();
                $productimage = $commandeInfo->getProduct()->getImage();
                $productQuantity = $commandeInfo->getQuantity();
            }
            $post_data = (object)['userFullName' => $userFullName,
                'etatCommande' => $etatCommande,
                'commandeId' => $commandeId,
                'totale' => $totale,
                'productname' => $productname,
                'productprice' => $productprice,
                'productQuantity' => $productQuantity,
                'productimage' => $productimage
            ];
            array_push($data, $post_data);
        }
        $response = new Response(json_encode(array('data' => $data)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/validCommande/{id}", name="valid_commands")
     */
    public function validerCommande(int                $id,
                                    CommandeRepository $commandeRepository,
                                    ManagerRegistry    $managerRegistry)
    {



        $manager = $managerRegistry->getManager();
        $commande = $commandeRepository->find($id);
        $commande->setEtat("Validé");
        $manager->persist($commande);
        $manager->flush();
        return $this->render('admin/index.html.twig', []);
    }

    /**
     * @Route("/refuseCommand/{id}", name="refuse_commands")
     */
    public function refuserCommande(int                $id,
                                    CommandeRepository $commandeRepository,
                                    ManagerRegistry    $managerRegistry)
    {

        $manager = $managerRegistry->getManager();
        $commande = $commandeRepository->find($id);
        $commande->setEtat("Refusé");
        $manager->persist($commande);
        $manager->flush();
        return $this->redirectToRoute('app_admin');

    }


}

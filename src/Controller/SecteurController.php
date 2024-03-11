<?php

namespace App\Controller;

use App\Entity\Secteur;
use App\Form\SecteurType;
use App\Repository\SecteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Knp\Component\Pager\PaginatorInterface;

class SecteurController extends AbstractController
{
    private $em;
    private   $serializer;
    private $clientWeb;
    private $paginator;

    private $myFunction;
    public function __construct(

        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction,


    ) {
        $this->em = $em;

        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
    }
    #[Route('/secteur/read', name: 'index', methods: ['GET'])]
    public function index(Request $request)
    {

        $secteurs = $this->em->getRepository(Secteur::class)->findAll();


        return new JsonResponse([

            'data' =>    array_map(function (Secteur $secteur) {

                return   [

                    'id' =>    $secteur->getId(),
                    'libelle' =>   $secteur->getLibelle(),
                    'status'
                    =>  true,
                    'dateCreated'
                    => date_format($secteur->getDateCreated(), 'Y-m-d H:i'),
                ];
            }, $secteurs),

        ], 200);
    }

    #[Route('/secteur/new', name: 'app_secteur_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $secteur = new Secteur();
        $secteur->setLibelle($request->toArray()['libelle']);

        $entityManager->persist($secteur);
        $entityManager->flush();



        return new JsonResponse([

            'data' =>   [

                'id' =>    $secteur->getId(),
                'libelle' =>   $secteur->getLibelle(),
                'status'
                =>  true,
                'dateCreated'
                => date_format($secteur->getDateCreated(), 'Y-m-d H:i'),

            ]

        ], 201);
    }

    #[Route('/secteur/{id}', name: 'app_secteur_show', methods: ['GET'])]
    public function show(Secteur $secteur): Response
    {
        return new JsonResponse([

            'data' =>  $secteur,

        ], 200);
    }

    #[Route('/secteur/{id}/edit', name: 'app_secteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Secteur $secteur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SecteurType::class, $secteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_secteur_index', [], Response::HTTP_SEE_OTHER);
        }
        return new JsonResponse([

            'data' =>  $secteur,

        ], 201);
    }

    #[Route('/secteur/{id}', name: 'app_secteur_delete', methods: ['POST'])]
    public function delete(Request $request, Secteur $secteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $secteur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($secteur);
            $entityManager->flush();
        }
        return new JsonResponse([

            'message' => 'ok',

        ], 201);
    }
}

<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Medicament;
use App\Entity\MedicamentObject;
use App\Entity\Livraison;
use App\Entity\LivraisonKey;
use App\Entity\LivraisonOrdonnanceKey;
use App\Entity\Mission;
use App\Entity\Ordonnance;
use App\Entity\OrdonnanceMedicament;
use App\Entity\PointLocalisation;
use App\Entity\Service;
use App\Entity\UserPlateform;
use App\Entity\Ville;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Filesystem;
use FFI\Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use DateTime;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

class MissionController extends AbstractController
{

    private $em;
    private   $serializer;
    private $clientWeb;
    private $paginator;

    private $myFunction;
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        HttpClientInterface $clientWeb,
        MyFunction
        $myFunction,

        PaginatorInterface $paginator

    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;

        $this->clientWeb = $clientWeb;
        $this->paginator = $paginator;
    }

    #[Route('/missions', name: 'NewMission', methods: ['POST'])]
    public function
    NewMission(Request $request)
    {

        $dataRequest = $request->toArray();
        $libelle =
            $dataRequest['libelle'];
        $description =
            $dataRequest['description'];
        $nbre_point =
            $dataRequest['nbre_point'];
        $mission = new Mission();

        $mission->setLibelle($libelle);
        $mission->setDescription($description);
        $mission->setNbrePoint($nbre_point);


        $this->em->persist($mission);
        $this->em->flush();
        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
    #[Route('/missions', name: 'ReadMission', methods: ['GET'])]
    public function
    ReadMission(Request $request)
    {

        $listmission = $this->em->getRepository(Mission::class)->findAll();
        return new JsonResponse(
            [
                'data'
                =>
                array_map(function (Mission $da) {

                    return   $this->myFunction->formatMission($da);
                },  $listmission)

            ],
            200
        );
    }
}

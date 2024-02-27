<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ListMissionBiker;
use App\Entity\Medicament;
use App\Entity\MedicamentObject;
use App\Entity\Livraison;
use App\Entity\LivraisonKey;
use App\Entity\LivraisonOrdonnanceKey;
use App\Entity\Mission;
use App\Entity\MissionSession;
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

class BikerController extends AbstractController
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

    #[Route('/biker/start-mission', name: 'StartMissionBiker', methods: ['POST'])]
    public function
    StartMissionBiker(Request $request)
    {

        $dataRequest = $request->toArray();
        $mission_id =
            $dataRequest['mission_id'];
        $mission  = $this->em->getRepository(Mission::class)->findOneBy(['id' => $mission_id]);
        $keySecret =
            $dataRequest['keySecret'];
        $biker  = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $missionBiker = $this->em->getRepository(ListMissionBiker::class)->findOneBy(['mission' => $mission, 'biker' => $biker]);
        if (!$missionBiker) {
            return new JsonResponse([
                'message' => 'Une erreur est survenue',
            ], 203);
        }
        $ms = new MissionSession();
        $ms->setMissionbiker($missionBiker);

        $this->em->persist($ms);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success Create Mission',
            'missionsession_id' => $ms->getId()
        ], 201);
    }

    #[Route('/biker/end-mission', name: 'EndMissionBiker', methods: ['POST'])]
    public function
    EndMissionBiker(Request $request)
    {

        $dataRequest = $request->toArray();
        $missionSession =
            $dataRequest['missionSession'];
        $ms = $this->em->getRepository(MissionSession::class)->find($missionSession);

        $ms->setEndMission(true);

        $this->em->persist($ms);
        $this->em->flush();
        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }

    #[Route('/biker/save-mission-location-point', name: 'SaveMissionLocationPointBiker', methods: ['POST'])]
    public function
    SaveMissionLocationPointBiker(Request $request)
    {
        $dataRequest = $request->toArray();

        $missionSession =
            $dataRequest['missionSession'];
        $longitude =
            $dataRequest['longitude'];
        $latitude =
            $dataRequest['latitude'];
        $missionsavebiker = $this->em->getRepository(MissionSession::class)->find($missionSession);


        $point = new PointLocalisation();
        $point->setLongitude($longitude);
        $point->setLatitude($latitude);
        $point->setMissionsavebiker($missionsavebiker);

        $this->em->persist($point);
        $this->em->flush();


        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
    #[Route('/biker/list-mission-session', name: 'ListMissionSession', methods: ['GET'])]
    public function
    ListMissionSession(Request $request)
    {

        $mission_id =
            $request->get('mission_id');
        $mission  = $this->em->getRepository(Mission::class)->findOneBy(['id' => $mission_id]);
        $keySecret =
            $request->get('keySecret');
        $biker  = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $missionBiker = $this->em->getRepository(ListMissionBiker::class)->findOneBy(['mission' => $mission, 'biker' => $biker]);
        if (!$missionBiker) {
            return new JsonResponse([
                'message' => 'Une erreur est survenue',
            ], 203);
        }
        $missionBiker = $this->em->getRepository(MissionSession::class)->findBy(['missionbiker' =>  $missionBiker]);

        return new JsonResponse(
            [
                'data'
                =>
                array_map(function (MissionSession $da) {

                    return   $this->myFunction->formatMissionSession($da);
                },  $missionBiker)

            ],
            200
        );
    }
    #[Route('/biker/list-mission', name: 'ListMissionBiker', methods: ['GET'])]
    public function
    ListMissionBiker(Request $request)
    {



        $keySecret =
            $request->get('keySecret');
        $biker = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        // $listmissionBiker = $this->em->getRepository(ListMissionBiker::class)->findOneBy(['biker' => $biker, 'mission.status' => true]);
        $listmissionBiker = $this->em->getRepository(ListMissionBiker::class)->createQueryBuilder('lmb')
            ->leftJoin('lmb.mission', 'm') // Assuming 'mission' is the property representing the Mission association in ListMissionBiker entity
            ->where('lmb.biker = :biker')
            ->andWhere('m.status = :status')
            ->setParameter('biker', $biker)
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
        return new JsonResponse(
            [
                'data'
                =>
                array_map(function (ListMissionBiker $da) {

                    return   $this->myFunction->formatMissionForUser($da);
                },  $listmissionBiker)

            ],
            200
        );
    }


    #[Route('/biker/list-mission-done', name: 'ListMissionBikerDone', methods: ['GET'])]
    public function
    ListMissionBikerDone(Request $request)
    {


        $keySecret =
            $request->get('keySecret');
        $biker  = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $missionBiker = $this->em->getRepository(ListMissionBiker::class)->findBy(['biker' => $biker]);
        if (!$missionBiker) {
            return new JsonResponse([
                'message' => 'Une erreur est survenue',
            ], 203);
        }

        return new JsonResponse(
            [
                'data'
                =>
                array_map(function (ListMissionBiker $da) {

                    return   $this->myFunction->formatMissionForUser($da);
                },  $missionBiker)

            ],
            200
        );
    }
}

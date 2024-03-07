<?php

namespace App\Controller;

use App\Entity\ControlMission;
use App\Entity\MedicamentPharmacie;
use App\Entity\UserPlateform;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Knp\Component\Pager\PaginatorInterface;

class CTerrainController extends AbstractController
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

    #[Route('/cterrain/start-control-biker', name: 'StartControlBiker', methods: ['POST'])]
    public function
    StartControlBiker(Request $request)
    {

        $dataRequest = $request->toArray();

        if (empty($dataRequest['idControl'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }


        $cmission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $cmission->setDateStart(new \DateTime());
        $this->em->persist($cmission);
        $this->em->flush();


        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
    #[Route('/cterrain/end-control-biker', name: 'EndControlBiker', methods: ['POST'])]
    public function
    EndControlBiker(Request $request)
    {
        $dataRequest = $request->toArray();

        if (empty($dataRequest['idControl'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }


        $cmission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $cmission->setDateEnd(new \DateTime());
        $cmission->setStatus(1);
        $this->em->persist($cmission);
        $this->em->flush();



        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }

    #[Route('/cterrain/note-biker', name: 'NoteBiker', methods: ['POST'])]
    public function
    NoteBiker(Request $request)
    {
        $dataRequest = $request->toArray();

        if (empty($dataRequest['idControl'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }


        $cmission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $cmission->setNote(new \DateTime());
        $this->em->persist($cmission);
        $this->em->flush();



        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }


    #[Route('/cterrain/list-mission', name: 'ListMissionCTerrain', methods: ['GET'])]
    public function
    ListMissionCTerrain(Request $request)
    {
        if (empty($dataRequest['keySecretCterrain'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $Cterrain = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' =>  $dataRequest['keySecretCterrain']]);

        $cmission = $this->em->getRepository(ControlMission::class)->findBy(['Cterrain' => $Cterrain, 'dateStart' => null, 'dateEnd' => null, 'status' => 0]);

        return new JsonResponse([
            'data' =>

            array_map(function (ControlMission $da) {

                return   $this->myFunction->formatMissionControl($da);
            }, $cmission)
        ], 200);
    }


    #[Route('/cterrain/list-mission-done', name: 'ListMissionCTerrainDone', methods: ['GET'])]
    public function
    ListMissionCTerrainDone(Request $request)
    {

        if (empty($dataRequest['keySecretCterrain'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $Cterrain = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' =>  $dataRequest['keySecretCterrain']]);

        $cmission = $this->em->getRepository(ControlMission::class)->findBy(['Cterrain' => $Cterrain,  'status' => 1]);

        return new JsonResponse([
            'data'
            =>    array_map(function (ControlMission $da) {

                return   $this->myFunction->formatMissionControl($da);
            }, $cmission)
        ], 200);
    }
}

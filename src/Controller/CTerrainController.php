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


        $control_mission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $control_mission->setDateStart(new \DateTime());
        $control_mission->setStatus(1);
        $this->em->persist($control_mission);
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


        $control_mission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $control_mission->setDateEnd(new \DateTime());
        $control_mission->setStatus(2);
        $this->em->persist($control_mission);
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


        $control_mission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $control_mission->setNote($dataRequest['note']);
        $this->em->persist($control_mission);
        $this->em->flush();



        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }


    #[Route('/cterrain/list-mission', name: 'ListMissionCTerrain', methods: ['GET'])]
    public function
    ListMissionCTerrain(Request $request)
    {
        if (empty($request->get('keySecretCterrain'))) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $Cterrain = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecretCterrain')]);

        $control_mission = $this->em->getRepository(ControlMission::class)->findBy(['CTerrain' => $Cterrain, 'status' => 0]);

        return new JsonResponse([

            'data' =>

            array_map(function (ControlMission $da) {

                return   $this->myFunction->formatMissionControl($da);
            }, $control_mission)
        ], 200);
    }


    #[Route('/cterrain/list-mission-done', name: 'ListMissionCTerrainDone', methods: ['GET'])]
    public function
    ListMissionCTerrainDone(Request $request)
    {

        if (empty($request->get('keySecretCterrain'))) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $Cterrain = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $request->get('keySecretCterrain')]);

        $control_mission = $this->em->getRepository(ControlMission::class)->findBy(['CTerrain' => $Cterrain,  'status' => 2]);

        return new JsonResponse([
            'data'
            =>    array_map(function (ControlMission $da) {

                return   $this->myFunction->formatMissionControl($da);
            }, $control_mission)
        ], 200);
    }
}

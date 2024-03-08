<?php

namespace App\Controller;

use App\Entity\ControlMission;
use App\Entity\ListMissionBiker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Medicament;
use App\Entity\Mission;
use App\Entity\MissionSession;
use App\Entity\TypeUser;
use App\Entity\UserPlateform;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Knp\Component\Pager\PaginatorInterface;

class CBureauController extends AbstractController
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


    #[Route('/cbureau/set-cterrain', name: 'SetCTerrain', methods: ['POST'])]
    public function
    SetCTerrain(Request $request)
    {

        $dataRequest = $request->toArray();


        if (empty($dataRequest['keySecretCbureau']) || empty($dataRequest['keySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $keySecret =
            $dataRequest['keySecret'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 3]);
        $user->setTypeUser($typeUser);

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
    #[Route('/cbureau/activate-biker', name: 'ActivateBiker', methods: ['POST'])]
    public function
    ActivateBiker(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['keySecretCbureau']) || empty($dataRequest['keySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $keySecret =
            $dataRequest['keySecret'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $user->setStatus(!$user->isStatus());

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }

    #[Route('/cbureau/affect-biker', name: 'AffectBikerToMission', methods: ['POST'])]
    public function
    AffectBikerToMission(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['mission_id']) || empty($dataRequest['keySecretCbureau']) || empty($dataRequest['keySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $keySecret =
            $dataRequest['keySecret'];
        $mission_id =
            $dataRequest['mission_id'];

        $mission = $this->em->getRepository(Mission::class)->findOneBy(['id' => $mission_id]);
        $biker = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $affectMission = new    ListMissionBiker();
        $affectMission->setMission($mission);
        $affectMission->setBiker($biker);
        $affectMission->setStatus(true);

        $this->em->persist($affectMission);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 200);
    }

    #[Route('/cbureau/unaffect-biker', name: 'UnAffectBikerToMission', methods: ['POST'])]
    public function
    UnAffectBikerToMission(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['mission_id']) || empty($dataRequest['keySecretCbureau']) || empty($dataRequest['keySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }
        $keySecret =
            $dataRequest['keySecret'];
        $mission_id =
            $dataRequest['mission_id'];

        $mission = $this->em->getRepository(Mission::class)->findOneBy(['id' => $mission_id]);
        $biker = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $affectMission = $this->em->getRepository(ListMissionBiker::class)->findBy(['mission' => $mission, 'biker' => $biker,]);
        if ($affectMission) {


            $affectMission->setStatus(false);

            $this->em->persist($affectMission);
            $this->em->flush();
        }
        return new JsonResponse([
            'message' => 'Success',
        ], 200);
    }

    #[Route('/cbureau/new-control', name: 'NewControlBiker', methods: ['POST'])]
    public function
    NewControlBiker(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['keySecretCbureau']) || empty($dataRequest['keySecretCterrain']) || empty($dataRequest['idMissionSession'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }


        $Cbureau = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' =>  $dataRequest['keySecretCbureau']]);
        $Cterrain = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' =>  $dataRequest['keySecretCterrain']]);
        $missionSave = $this->em->getRepository(MissionSession::class)->findOneBy(['id' =>  $dataRequest['idMissionSession']]);

        $cmission = new ControlMission();
        $cmission->setCTerrain($Cterrain);
        $cmission->setCBureau(
            $Cbureau
        );
        $cmission->setBikerMission($missionSave);
        $this->em->persist($cmission);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }


    #[Route('/cbureau/list-mission-session', name: 'ListMissionSession', methods: ['GET'])]
    public function
    ListMissionSession(Request $request)
    {
        // if (empty($dataRequest['keySecretCterrain'])) {

        //     return new JsonResponse([
        //         'message' => 'Veuillez reessayer'
        //     ], 203);
        // }
        $missionSave = $this->em->getRepository(MissionSession::class)->findBy(['endMission' => false]);

        return new JsonResponse([
            'data' =>

            array_map(function (MissionSession $da) {

                return   $this->myFunction->formatMissionSessionN($da);
            }, $missionSave)
        ], 200);
    }

    #[Route('/cbureau/annul-control', name: 'AnnulControlBiker', methods: ['POST'])]
    public function
    AnnulControlBiker(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['idControl'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }


        $cmission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $cmission->setStatus(false);
        $this->em->persist($cmission);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
    #[Route('/cbureau/active-control', name: 'ActiveControlBiker', methods: ['POST'])]
    public function
    ActiveControlBiker(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['idControl'])) {

            return new JsonResponse([
                'message' => 'Veuillez reessayer'
            ], 203);
        }


        $cmission = $this->em->getRepository(ControlMission::class)->findOneBy(['id' =>  $dataRequest['idControl']]);

        $cmission->setStatus(false);
        $this->em->persist($cmission);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
    #[Route('/cbureau/list-control', name: 'ListMissionCBureau', methods: ['GET'])]
    public function
    ListMissionCBureau(Request $request)
    {


        $cmission = $this->em->getRepository(ControlMission::class)->findAll();


        return new JsonResponse([
            'data' =>

            array_map(function (ControlMission $da) {

                return   $this->myFunction->formatMissionControl($da);
            }, $cmission)
        ], 201);
    }
    #[Route('/cbureau/list-control-done', name: 'ListMissionCBureauDone', methods: ['GET'])]
    public function
    ListMissionCBureauDone(Request $request)
    {

        $dataRequest = $request->toArray();


        // if (empty($dataRequest['keySecretCBureau']) || empty($dataRequest['keySecret'])) {

        //     return new JsonResponse([
        //         'message' => 'Veuillez reessayer'
        //     ], 203);
        // }
        // $keySecret =
        //     $dataRequest['keySecret'];
        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);

        // $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 3]);
        // $user->setTypeUser($typeUser);

        // $this->em->persist($user);
        // $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 201);
    }
}

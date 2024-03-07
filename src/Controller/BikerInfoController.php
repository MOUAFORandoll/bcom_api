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

use App\Entity\InfoBiker;
use App\Entity\ObjectFile;
use App\Repository\InfoBikerRepository;

class BikerInfoController extends AbstractController
{

    private $em;
    private   $serializer;
    private $mailer;
    private $clientWeb;
    private $passwordEncoder;
    private $jwt;
    private $jwtRefresh;
    private $validator;
    private $myFunction;
    private $paginator;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,

        MyFunction  $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
    }

    #[Route('/info-biker', name: 'info_biker_list', methods: ['GET'])]
    public function index(InfoBikerRepository $infoBikerRepository): JsonResponse
    {
        $infoBikers = $infoBikerRepository->findAll();

        return $this->json($infoBikers);
    }

    #[Route('/info-biker/{id}', name: 'info_biker_show', methods: ['GET'])]
    public function show(InfoBiker $infoBiker): JsonResponse
    {
        return $this->json($infoBiker);
    }

    #[Route('/info-biker', name: 'info_biker_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): JsonResponse
    {


        $data = [
            'gender' => $request->get('gender'),
            'age' => $request->get('age'),
            'cni' => $request->get('cni'),
            'handicap' => $request->get('handicap'),
            'handicap_description' => $request->get('handicap_description'),
            'is_biker' => $request->get('is_biker'),
            'is_biker_yes' => $request->get('is_biker_yes'),
            'is_biker_no' => $request->get('is_biker_no'),
            'is_syndicat' => $request->get('is_syndicat'),
            'is_syndicat_yes' => $request->get('is_syndicat_yes'),
            'have_moto' => $request->get('have_moto'),
            'num_carte_grise_moto' => $request->get('num_carte_grise_moto'),
            'bike_work_time' => $request->get('bike_work_time'),
            'keySecret' => $request->get('keySecret'),

        ];


        $user = $entityManager->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Desolez l\'utilisateur en question a des contraintes',

            ], 203);
        }

        $infoBiker = new InfoBiker();
        $infoBiker->setGender($data['gender']);
        $infoBiker->setAge($data['age']);
        $infoBiker->setCni($data['cni']);
        $infoBiker->setHandicap($data['handicap']);
        $infoBiker->setHandicapDescription($data['handicap_description']);
        $infoBiker->setIsBiker($data['is_biker']);
        $infoBiker->setIsBikerYes($data['is_biker_yes']);
        $infoBiker->setIsBikerNo($data['is_biker_no']);
        $infoBiker->setIsSyndicat($data['is_syndicat']);
        $infoBiker->setIsSyndicatYes($data['is_syndicat_yes']);
        $infoBiker->setHaveMoto($data['have_moto']);
        $infoBiker->setNumCarteGriseMoto($data['num_carte_grise_moto']);
        $infoBiker->setBikeWorkTime($data['bike_work_time']);
        $infoBiker->setBiker($user);



        $cni_avant = $request->files->get('cni_avant');
        if ($cni_avant != null) {
            $idCniA =     $this->updateFile(0,   $cni_avant, $slugger);
            $infoBiker->setCniAvant($idCniA);
        }

        $cni_arriere = $request->files->get('cni_arriere');
        if ($cni_arriere != null) {

            $idCniAr =     $this->updateFile(1,   $cni_arriere, $slugger);
            $infoBiker->setCniArriere($idCniAr);
        }

        $carte_grise = $request->files->get('carte_grise');
        if ($carte_grise != null) {
            $idCG =     $this->updateFile(2,   $carte_grise, $slugger);
            $infoBiker->setCarteGrise($idCG);
        }
        $entityManager->persist($infoBiker);
        $entityManager->flush();

        $profile      = count($user->getUserObjects())  == 0 ? '' : $user->getUserObjects()->last()->getSrc();
        $userU = [
            'id' => $user->getId(),
            'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
            'email' => $user->getEmail() ?? '', 'phone' => $user->getPhone(),
            'status' => $user->isStatus(),
            'typeUser' => $user->getTypeUser()->getId(),
            'infoComplete' => $user->getTypeUser()->getId() == 4 ? count($user->getInfoBikers()) != 0 : true,
            'profile' => $this->myFunction::BACK_END_URL . '/images/users/' . $profile,


            'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

        ];


        return new JsonResponse([

            'data' =>  $userU,

        ], 201);
    }
    public function updateFile($type, $file, $slugger)
    {
        $object = null;
        if ($type == 0) {
            $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $newFilenameData =
                $this->myFunction->getUniqueNamCni() .  '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('biker_object'),
                $newFilenameData
            );
            $object = new ObjectFile();

            $object->setSrc($newFilenameData);

            $this->em->persist($object);
            $this->em->flush();
        }
        if ($type == 1) {
            $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilenameData = $slugger->slug($originalFilenameData);
            $newFilenameData =
                $this->myFunction->getUniqueNamCni() .  '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('biker_object'),
                $newFilenameData
            );
            $object = new ObjectFile();

            $object->setSrc($newFilenameData);

            $this->em->persist($object);
            $this->em->flush();
        }
        if ($type == 2) {

            $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $safeFilenameData = $slugger->slug($originalFilenameData);
            $newFilenameData =
                $this->myFunction->getUniqueNamCGrise() .  '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('biker_object'),
                $newFilenameData
            );
            $object = new ObjectFile();

            $object->setSrc($newFilenameData);

            $this->em->persist($object);
            $this->em->flush();
        }

        return
            $object;
    }

    #[Route('/info-biker/{id}', name: 'info_biker_update', methods: ['PUT'])]
    public function update(Request $request, InfoBiker $infoBiker, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $infoBiker->setGender($data['gender']);
        $infoBiker->setCni($data['cni']);
        $infoBiker->setHandicap($data['handicap']);
        $infoBiker->setHandicapDescription($data['handicap_description']);
        $infoBiker->setIsBiker($data['is_biker']);
        $infoBiker->setIsBikerYes($data['is_biker_yes']);
        $infoBiker->setIsBikerNo($data['is_biker_no']);
        $infoBiker->setIsSyndicat($data['is_syndicat']);
        $infoBiker->setIsSyndicatYes($data['is_syndicat_yes']);
        $infoBiker->setHaveMoto($data['have_moto']);
        $infoBiker->setNumCarteGriseMoto($data['num_carte_grise_moto']);
        $infoBiker->setBikeWorkTime($data['bike_work_time']);
        // Ajoutez le reste des setters pour les autres propriétés

        $entityManager->flush();

        return $this->json($infoBiker);
    }

    #[Route('/info-biker/{id}', name: 'info_biker_delete', methods: ['DELETE'])]
    public function delete(InfoBiker $infoBiker, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($infoBiker);
        $entityManager->flush();

        return $this->json(['message' => 'InfoBiker deleted']);
    }
}

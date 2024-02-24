<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Medicament;
use App\Entity\TypeUser;
use App\Entity\UserPlateform;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\FunctionU\MyFunction;
use Knp\Component\Pager\PaginatorInterface;

class AdminController extends AbstractController
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
    #[Route('/cbureau/set-cbureau', name: 'SetCbureau', methods: ['POST'])]
    public function SetCbureau(Request $request)
    {

        $dataRequest = $request->toArray();


        if (empty($dataRequest['keySecretAdmin']) || empty($dataRequest['keySecret'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre numero de telephone ou votre adresse mail et le code'
            ], 203);
        }
        $keySecret =
            $dataRequest['keySecret'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 2]);
        $user->setTypeUser($typeUser);

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 200);
    }
}

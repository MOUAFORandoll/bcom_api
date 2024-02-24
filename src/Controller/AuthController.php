<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\UserObject;
use App\Entity\UserPlateform;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swift_Mailer;
use Swift_SendmailTransport;
use Symfony\Component\Serializer\SerializerInterface;
use Swift_SmtpTransport;
use Swift_Transport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\FunctionU\MyFunction;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\EmailService;
use App\Entity\TypeUser;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
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
        MailerInterface $mailer,
        HttpClientInterface $clientWeb,
        JWTTokenManagerInterface $jwt,
        PaginatorInterface $paginator,
        RefreshTokenManagerInterface $jwtRefresh,
        UserPasswordHasherInterface    $passwordEncoder,
        ValidatorInterface
        $validator,
        MyFunction  $myFunction
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->myFunction = $myFunction;
        $this->passwordEncoder = $passwordEncoder;
        $this->clientWeb = $clientWeb;
        $this->jwt = $jwt;
        $this->jwtRefresh = $jwtRefresh;
        $this->paginator = $paginator;

        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/auth/dashboard", name="authDashboard", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function authDashboard(Request $request)
    {
        $data = $request->toArray();


        if (empty($data['phone'])   || empty($data['password'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre numero de telephone et mot de passe.'
            ], 203);
        }

        $phone = $data['phone'];
        $password = $data['password'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['phone' => $phone]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Ce client n\'existe pas'
            ], 203);
        }
        if ($user->getTypeUser()->getId() != 1 || $user->getTypeUser()->getId() != 2) {
            return new JsonResponse([
                'message' => 'Ce client n\'est pas administrateur'
            ], 203);
        }


        if (!password_verify($password, $user->getPassword())) {
            return new JsonResponse([
                'message' => 'Mauvais mot de passe'
            ], 203);
        }
        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse([


            'token' => $tokenAndRefresh->token,
            'refreshToken' => $tokenAndRefresh->refreshToken,
        ], 201);
    }
    /**
     * @Route("/auth/user", name="authUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function authUser(Request $request)
    {
        $data = $request->toArray();


        if (empty($data['phone'])   || empty($data['password'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre numero de telephone et mot de passe.'
            ], 203);
        }

        $phone = $data['phone'];
        $password = $data['password'];
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['phone' => $phone]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Ce client n\'existe pas'
            ], 203);
        }

        if (!password_verify($password, $user->getPassword())) {
            return new JsonResponse([
                'message' => 'Mauvais mot de passe'
            ], 203);
        }
        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse([


            'token' => $tokenAndRefresh->token,
            'refreshToken' => $tokenAndRefresh->refreshToken,
        ], 201);
    }
    /**
     * @Route("/auth/create-user", name="createUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createUser(Request $request)
    {
        $data = $request->toArray();


        if (empty($data['nom']) || empty($data['phone'])   || empty($data['password'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre nom, prenom, numero de telephone et mot de passe.'
            ], 203);
        }

        $nom = $data['nom'];
        $phone = $data['phone'];
        $password = $data['password'];
        $typeCompte = $data['typeCompte'];

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['phone' => $phone]);


        if ($user) {
            return new JsonResponse([
                'message' => 'Numero de telephone deja utilise'
            ], 203);
        }
        $otherNumber = [];

        for ($i = 0; $i < 4; $i++) {
            try {
                $otherNumber[] = random_int(0, 9);
            } catch (\Exception $e) {
                echo $e;
            }
        }

        $keySecret = password_hash(($phone . '' . $password . '' . (new \DateTime())->format('Y-m-d H:i:s') . '' . implode("", $otherNumber)), PASSWORD_DEFAULT);

        if (strlen($keySecret) > 100) {
            $keySecret = substr($keySecret, 0, 99);
        }


        $user = new UserPlateform();
        $user->setNom($nom);
        $user->setPrenom('');

        $user->setPhone($phone);
        $user->setPassword($password);

        $user->setKeySecret($keySecret);
        $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => $typeCompte]);
        $user->setTypeUser($typeUser);

        $passwordN = $this->passwordEncoder->hashPassword(
            $user,
            $password
        );
        $user->setPassword($passwordN);
        $this->em->persist($user);
        $this->em->flush();
        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse([
            'user' => $user,
            'token' => $tokenAndRefresh->token,
            'refreshToken' => $tokenAndRefresh->refreshToken,
        ], 201);
    }
    /**
     * @Route("/user/get", name="getUserX", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserX(Request $request)
    {

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $request->get('id')]);


        if (!$user) {
            return new JsonResponse([
                'message' => 'Desolez l\'utilisateur en question a des contraintes',

            ], 203);
        }

        $profile      = count($user->getUserObjects())  == 0 ? '' : $user->getUserObjects()->last()->getSrc();
        // $user->getUserObjects()[count($user->getUserObjects()) - 1]->getSrc();
        $userU = [
            'id' => $user->getId(),
            'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
            'email' => $user->getEmail() ?? '', 'phone' => $user->getPhone(),
            'status' => $user->isStatus(),
            'typeUser' => $user->getTypeUser()->getId(),
            'profile' => $this->myFunction::BACK_END_URL . '/images/users/' . $profile,


            'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),
            // 'localisation' =>    $localisation  ? [
            //     'ville' =>
            //     $localisation->getVille(),

            //     'longitude' =>
            //     $localisation->getLongitude(),
            //     'latitude' =>
            //     $localisation->getLatitude(),
            // ] : []
            // 'nom' => $user->getNom()
        ];



        return new JsonResponse([
            // 'status' => 'ok',
            'data' =>  $userU,

        ], 200);
    }
    /**
     * @Route("/auth/user-update", name="updateProfilClient", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfilClient(Request $request)
    {
        $data = $request->toArray();
        if (empty($data['keySecret'])) {
            return new JsonResponse([
                'message' => 'Veuillez recharger la page et reessayer '
            ], 203);
        }
        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Desolez l\'utilisateur en question n\'existe pas dans la base de donnée'
            ], 203);
        }

        if (!empty($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (!empty($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }
        if (!empty($data['email'])) {
            $user->setEmail($data['email']);
            $user->setRecupMailStatus(true);
        }


        if (!empty($data['phone'])) {
            $user->setPhone($data['phone']);
        }


        $this->em->persist($user);
        $this->em->flush();

        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse([
            'message' => 'success',
            'token' => $tokenAndRefresh->token,
            'refreshToken' => $tokenAndRefresh->refreshToken,
        ], 201);
    }


    /**
     * @Route("/auth/send-code", name="sendCodeForgotPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendCodeForgotPassword(Request $request,)
    {
        $data = $request->toArray();


        if (empty($data['data'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre numero de telephone ou votre adresse mail.'
            ], 203);
        }

        $data = $data['data'];
        $isValidPhoneNumber = preg_match('/^\d{9}$/', $data);

        $isValidEmail = filter_var($data, FILTER_VALIDATE_EMAIL);

        // Validate either as a phone number or an email address
        if (!$isValidPhoneNumber && !$isValidEmail) {

            // $data is not valid
            return new JsonResponse([
                'message' => 'Invalid phone number or email address',
            ], 203);
        }
        $user
            = $isValidEmail ?  $this->em->getRepository(UserPlateform::class)->findOneBy([

                'email' => $data
            ]) : $this->em->getRepository(UserPlateform::class)->findOneBy([
                'phone' => $data
            ]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Ce client n\'existe pas'
            ], 203);
        }

        $code =
            $this->createCode();
        $user->setCodeRecup($code);
        $this->em->persist($user);
        $this->em->flush();
        $statusCode = $isValidEmail ?
            $this->myFunction->sendMail($data, 'Your Subject', 'Your email body content') :
            $this->sendCode($data, $code);
        if ($statusCode) {
            return new JsonResponse([
                'message' => $statusCode
            ],  201);
        } else {
            return new JsonResponse([
                'message' => "Veuillez reessayer, une erreur est survenue"
            ],  203);
        }
    }
    /**
     * @Route("/auth/verify-code", name="verifyCodeForgotPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyCodeForgotPassword(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['code']) || empty($dataRequest['data'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre numero de telephone ou votre adresse mail et le code'
            ], 203);
        }

        $data = $dataRequest['data'];
        $code = $dataRequest['code'];
        $isValidPhoneNumber = preg_match('/^\d{9}$/', $data);



        $user =
            $isValidPhoneNumber  ?  $this->em->getRepository(UserPlateform::class)->findOneBy([
                'phone' => $data,
                'codeRecup' => $code,
            ]) : $this->em->getRepository(UserPlateform::class)->findOneBy([
                'email' => $data,
                'codeRecup' => $code,
            ]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Code incorrect'
            ], 203);
        }


        return new JsonResponse([
            'message' => 'success'

        ], 201);
    }


    /**
     * @Route("/auth/new-password", name="newPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function newPassword(Request $request)
    {
        $dataRequest = $request->toArray();


        if (empty($dataRequest['password']) || empty($dataRequest['data'])) {

            return new JsonResponse([
                'message' => 'Veuillez preciser votre numero de telephone ou votre adresse mail et le code'
            ], 203);
        }

        $data = $dataRequest['data'];
        $password = $dataRequest['password'];
        $isValidPhoneNumber = preg_match('/^\d{9}$/', $data);



        $user =
            $isValidPhoneNumber  ?  $this->em->getRepository(UserPlateform::class)->findOneBy([
                'phone' => $data

            ]) : $this->em->getRepository(UserPlateform::class)->findOneBy([
                'email' => $data

            ]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Ce client n\'existe pas'
            ], 203);
        }

        $passwordN = $this->passwordEncoder->hashPassword(
            $user,
            $password
        );
        $user->setPassword($passwordN);
        $this->em->persist($user);
        $this->em->flush();
        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse([
            'token' => $tokenAndRefresh->token,
            'refreshToken' => $tokenAndRefresh->refreshToken,
        ], 201);
    }
    public function getNewPssw(/* $id */)
    {

        $chaine = '';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }

        $chaine .= '@';
        for ($i = 0; $i < 2; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        // $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => $id]);
        // $password = $this->passwordEncoder->hashPassword(
        //     $user,
        //     $chaine
        // );
        // $user->setPassword($password);

        return $chaine;
    }
    /**
     * @Route("/update/password/user", name="updatePasswordClient", methods={"PATCH"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePasswordClient(Request $request)
    {
        // $data = $request->toArray();
        // if (empty($data['userId'])) {
        //     return new JsonResponse([
        //         'message' => 'Veuillez recharger la page et reessayerle userId et password sont requis'
        //     ], 203);
        // }
        // $user = $this->em->getRepository(UserPlateform::class)->find((int)$data['userId']);

        // if (!$user) {
        //     return new JsonResponse([
        //         'message' => 'Desolez l\'utilisateur en question n\'existe pas dans la base de donnée'
        //     ], 203);
        // }
        // $npass = $data['password'] ??   $this->getNewPssw();

        // $password = $this->passwordEncoder->encodePassword(
        //     $user,
        //     $npass
        // );
        // $user->setPassword($password);
        // // $user->setFirstConnexion(false);
        // $this->em->persist($user);
        // $this->em->flush();

        // $infoUser = $this->createNewJWT($user);
        // $tokenAndRefresh = json_decode($infoUser->getContent());

        // return new JsonResponse([
        //     'password' => $npass,
        //     'token' => $tokenAndRefresh->token,
        //     'refreshToken' => $tokenAndRefresh->refreshToken,
        // ], 200);
    }
    public function createNewJWT(UserPlateform $user)
    {
        $token = $this->jwt->create($user);

        $datetime = new \DateTime();
        $datetime->modify('+2592000 seconds');

        $refreshToken = $this->jwtRefresh->create();

        $refreshToken->setUsername($user->getUsername());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($datetime);

        // Validate, that the new token is a unique refresh token
        $valid = false;
        while (false === $valid) {
            $valid = true;
            $errors = $this->validator->validate($refreshToken);
            if ($errors->count() > 0) {
                foreach ($errors as $error) {
                    if ('refreshToken' === $error->getPropertyPath()) {
                        $valid = false;
                        $refreshToken->setRefreshToken();
                    }
                }
            }
        }

        $this->jwtRefresh->save($refreshToken);

        return new JsonResponse([
            'token' => $token,
            'refreshToken' => $refreshToken->getRefreshToken()
        ], 200);
    }


    public function sendCode($data,    $code)
    {
        return $code;
    }


    public function createCode()
    {

        $code = '';
        $listeCar = '0123456789';

        for ($i = 0; $i < 4; ++$i) {
            $code .= $listeCar[random_int(0, 9)];
        }
        $ExistTransaction = $this->em->getRepository(UserPlateform::class)->findOneBy(['codeRecup' => $code]);
        if ($ExistTransaction) {
            return
                $this->createCode();
        } else {
            return      $code;
        }
    }




    /**
     * @Route("/admin/read", name="adminReadAll", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     */
    public function adminReadAll(Request $request)
    {

        $typeCompte = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 1]);
        $data       = $request->toArray();
        $possible   = false;
        if (empty($data['keySecret'])) {

            return new JsonResponse([
                'message' => 'Mauvais parametre de requete veuillez preciser votre keySecret '
            ], 203);
        }
        $userUser = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $data['keySecret']]);
        if ($userUser) {
            $list_users_final = [];

            /**
             * si il a le role admin
             */
            if ($userUser->getTypeUser()->getId() == 1) {
                $luser = $this->em->getRepository(UserPlateform::class)->findAll();

                foreach ($luser as $user) {
                    if ($user->getTypeUser()->getId() == 1) {
                        $localisation = $user->getLocalisations()[count($user->getLocalisations()) - 1];
                        $userU        = [
                            'id' => $user->getId(),
                            'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                            'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                            'status' => $user->isStatus(),
                            'typeUser' => $user->getTypeUser()->getId(),
                            'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                            'localisation' => $localisation ? [
                                'ville' =>
                                $localisation->getVille(),

                                'longitude' =>
                                $localisation->getLongitude(),
                                'latitude' =>
                                $localisation->getLatitude(),
                            ] : [
                                'ville' =>
                                'Aucune',

                                'longitude' =>
                                0,
                                'latitude' =>
                                0,
                            ]
                            // 'nom' => $user->getNom()
                        ];

                        $list_users_final[] = $userU;
                    } else {
                    }
                }
                $datas
                    = $this->serializer->serialize(array_reverse($list_users_final), 'json');
                return
                    new JsonResponse([
                        'data'
                        =>
                        JSON_DECODE($datas),

                    ], 200);
            } else {
                return new JsonResponse([
                    'data'
                    => [],
                    'message' => 'Action impossible'
                ], 200);
            }
        } else {
            return new JsonResponse([
                'data'
                => [],
                'message' => 'Action impossible'
            ], 200);
        }
    }




    /**
     * @Route("/user/image/new", name="UserImage", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     * @param array $data doit contenir la  la keySecret du
     * 
     * 
     */
    public function UserImage(Request $request, SluggerInterface $slugger)
    {


        // $typeCompte = $AccountEntityManager->getRepository(TypeCompte::class)->findOneBy(['id' => 1]);

        $possible = false;





        if (
            empty($request->get('keySecret'))

        ) {
            return new JsonResponse(
                [
                    'message' => 'Une erreur est survenue'
                ],
                203
            );
        }

        $keySecret = $request->get('keySecret');

        $user = $this->em->getRepository(UserPlateform::class)->findOneBy(['keySecret' => $keySecret]);
        if (
            !$user
        ) {
            return new JsonResponse(
                [
                    'message' => 'Utilisateur introuvable'
                ],
                203
            );
        }



        $file =  $request->files->get('file');

        $originalFilenameData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilenameData = $slugger->slug($originalFilenameData);
        $newFilenameData =
            $this->myFunction->getUniqueNamUserName() .  '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('users_object'),
            $newFilenameData
        );
        $UserObject = new UserObject();

        $UserObject->setSrc($newFilenameData);
        $UserObject->setUserPlateform($user);
        $this->em->persist($UserObject);
        $this->em->flush();

        $infoUser = $this->createNewJWT($user);
        $tokenAndRefresh = json_decode($infoUser->getContent());

        return new JsonResponse(
            [
                'token' => $tokenAndRefresh->token,
                'refreshToken' => $tokenAndRefresh->refreshToken,

                'message'
                =>  'success',

            ],
            201
        );
    }



    /**
     * @Route("/user/find", name="userFind", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function userFind(Request $request)
    {

        $users = [];
        $name =
            $request->get('name');


        $usersR = $this->em->getRepository(UserPlateform::class)->findByUserName($name);
        foreach ($usersR  as $user) {


            $users[] = [
                'id' => $user->getId(),
                'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                'status' => $user->isStatus(),
                'typeUser' => $user->getTypeUser()->getId(),
                'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                'keySecret' => $user->getKeySecret()
            ];
        }


        return new JsonResponse([
            'data' => $users,

        ], 200);
    }




    /**
     * @Route("/biker/read", name="bikerReadAll", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     */
    public function bikerReadAll(Request $request)
    {

        $typeCompteBiker = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 4]);

        $list_users_final = [];

        $luser = $this->em->getRepository(UserPlateform::class)->findBy(['typeUser' => $typeCompteBiker]);

        foreach ($luser as $user) {

            $userU        = [
                'id' => $user->getId(),
                'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                'status' => $user->isStatus(),
                'typeUser' => $user->getTypeUser()->getId(),
                'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                'keySecret' => $user->getKeySecret()
                // 'nom' => $user->getNom()
            ];

            $list_users_final[] = $userU;
        }
        $datas
            = $this->serializer->serialize(array_reverse($list_users_final), 'json');
        return
            new JsonResponse([
                'data'
                =>
                JSON_DECODE($datas),

            ], 200);
    }


    /**
     * @Route("/cBureau/read", name="CBureauReadAll", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     */
    public function CBureauReadAll(Request $request)
    {

        $typeCompteCBureau = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 2]);

        $list_users_final = [];

        $luser = $this->em->getRepository(UserPlateform::class)->findBy(['typeUser' => $typeCompteCBureau]);

        foreach ($luser as $user) {

            $userU        = [
                'id' => $user->getId(),
                'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                'status' => $user->isStatus(),
                'typeUser' => $user->getTypeUser()->getId(),
                'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                'keySecret' => $user->getKeySecret()
                // 'nom' => $user->getNom()
            ];

            $list_users_final[] = $userU;
        }
        $datas
            = $this->serializer->serialize(array_reverse($list_users_final), 'json');
        return
            new JsonResponse([
                'data'
                =>
                JSON_DECODE($datas),

            ], 200);
    }

    /**
     * @Route("/cTerrain/read", name="CTerrainReadAll", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     */
    public function CTerrainReadAll(Request $request)
    {

        $typeCompteCTerrain = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 3]);

        $list_users_final = [];

        $luser = $this->em->getRepository(UserPlateform::class)->findBy(['typeUser' => $typeCompteCTerrain]);

        foreach ($luser as $user) {

            $userU        = [
                'id' => $user->getId(),
                'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                'status' => $user->isStatus(),
                'typeUser' => $user->getTypeUser()->getId(),
                'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                'keySecret' => $user->getKeySecret()
                // 'nom' => $user->getNom()
            ];

            $list_users_final[] = $userU;
        }
        $datas
            = $this->serializer->serialize(array_reverse($list_users_final), 'json');
        return
            new JsonResponse([
                'data'
                =>
                JSON_DECODE($datas),

            ], 200);
    }
    /**
     * @Route("/admin/read", name="AdminRead", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     */
    public function AdminRead(Request $request)
    {

        $typeCompteAdmin = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 1]);

        $list_users_final = [];

        $luser = $this->em->getRepository(UserPlateform::class)->findBy(['typeUser' => $typeCompteAdmin]);

        foreach ($luser as $user) {

            $userU        = [
                'id' => $user->getId(),
                'nom' => $user->getNom(), 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(), 'phone' => $user->getPhone(),
                'status' => $user->isStatus(),
                'typeUser' => $user->getTypeUser()->getId(),
                'date_created' => date_format($user->getDateCreated(), 'Y-m-d H:i'),

                'keySecret' => $user->getKeySecret()
                // 'nom' => $user->getNom()
            ];

            $list_users_final[] = $userU;
        }
        $datas
            = $this->serializer->serialize(array_reverse($list_users_final), 'json');
        return
            new JsonResponse([
                'data'
                =>
                JSON_DECODE($datas),

            ], 200);
    }
    /**
     * @Route("/location/user", name="LocationUser", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * 
     * 
     * @param array $data doit contenir la  la keySecret du
     * 
     * 
     */
    public function LocationUser(Request $request)
    {

        $long = $request->get('long');
        $lat = $request->get('lat');
        $first =   $this->clientWeb->request('GET',   "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . ","   .  $long . "&sensor=true&key=" .  $_ENV['KEY']);
        $data = json_decode($first->getContent(), true);
        $results = $data['results'][1]["formatted_address"];

        return new JsonResponse(
            [
                'quartier' => explode(', ',  $results)[0],
                'ville' =>   explode(', ',  $results)[1] . ',' . explode(',',  $results)[2]

            ],
            200
        );
    }
}

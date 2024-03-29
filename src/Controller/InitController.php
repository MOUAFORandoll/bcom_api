<?php

namespace App\Controller;

use App\Entity\PointLocalisation;
use App\Entity\TypeUser;
use App\Entity\UserPlateform;
use App\Entity\Ville;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class InitController extends AbstractController
{
    private $em;
    private $mailer;
    private $publicDirectory;
    private $transactionFunction;
    public function __construct(
        EntityManagerInterface $em,
        ParameterBagInterface $params,
        MailerInterface $mailer
    ) {
        $this->em = $em;
        $this->publicDirectory = $params->get('kernel.project_dir') . '/public';

        $this->mailer = $mailer;
    }


    #[Route('/bcom/config', name: 'InitConfig', methods: ['GET'])]
    public function initConfig()
    {
        $directory = $this->createFileRepertory();
        $typeU = $this->initTypeUser();
        $ville = $this->initVille();
        return new JsonResponse([
            'type_user' => $typeU,
            'ville' => $ville,
            'directory' => $directory,
        ], 200);
    }


    #[Route('/create-directory', name: 'create_directory', methods: ['GET'])]
    public function createFileRepertory()
    {
        $basePath = $this->publicDirectory;
        $subDirs = ['images', 'images/users', 'images/biker_object']; // Exemple de sous-dossiers

        foreach ($subDirs as $subDir) {
            $fullPath = $basePath . '/' . $subDir;

            if (!file_exists($fullPath)) {
                if (mkdir($fullPath, 0777, true)) {
                    echo "Dossier '$fullPath' créé avec succès.<br>";
                } else {
                    echo "Erreur lors de la création du dossier '$fullPath'.<br>";
                }
            } else {
                echo "Le dossier '$fullPath' existe déjà.<br>";
            }
        }

        return new JsonResponse([
            'message' => 'Success',
        ], 200);
    }


    #[Route('/bcom/admin', name: 'AdminInit', methods: ['GET'])]
    public function AdminInit()
    {
        $admin = $this->em->getRepository(UserPlateform::class)->findOneBy(['id' => 1]);

        $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 1]);
        $admin->setTypeUser($typeUser);

        $this->em->persist($admin);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Success',
        ], 200);
    }


    private function initTypeUser()
    {
        $types = ['Admin', 'Controller Bureau', 'Controller Terrain', 'Biker'];
        $data = $this->em->getRepository(TypeUser::class)->findAll();

        if (count($data) >= count($types)) {
            return new JsonResponse(['message' => 'Exist'], 200);
        }

        foreach ($types as $typeName) {
            $type = new TypeUser();
            $type->setLibelle($typeName);

            $this->em->persist($type);
            $this->em->flush();
        }

        return new JsonResponse(['message' => 'Success'], 200);
    }

    private function initVille()
    {
        $villes = ['Douala', 'Yaounde', 'Bafoussam'];
        $data = $this->em->getRepository(Ville::class)->findAll();

        if (count($data) >= count($villes)) {
            return new JsonResponse(['message' => 'Exist'], 200);
        }

        foreach ($villes as $villeName) {
            $ville = new Ville();
            $ville->setLibelle($villeName);

            $this->em->persist($ville);
            $this->em->flush();
        }

        return new JsonResponse(['message' => 'Success'], 200);
    }

    #[Route('/mail', name: 'sendEmail', methods: ['GET'])]
    public function sendEmail()
    {
        $transport = Transport::fromDsn('smtp://admin@bcom.cm:NFju6%rwA33c@mx-dc03.ewodi.net:465/?timeout=60&encryption=ENCRYPTION_SMTPS&auth_mode=AUTH_MODE');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from(new Address('admin@bcom.cm'))
            ->to(new Address('hari.randoll@gmail.com'))
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return new JsonResponse(['message' => 'Success'], 200);
    }
}

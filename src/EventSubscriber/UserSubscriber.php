<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities; 
use App\Entity\TypeUser;
use App\Entity\UserPlateform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\FunctionU\MyFunction;

final class UserSubscriber extends AbstractController implements EventSubscriberInterface
{

    private $em;
    public    $doctrine;
    private $myFunction;

    public function __construct(
        EntityManagerInterface $em,
        MyFunction  $myFunction

    ) {
        $this->em = $em;
        $this->myFunction = $myFunction;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['operationAfterCreateAMember', EventPriorities::POST_WRITE]
        ];
    }

    public function operationAfterCreateAMember(ViewEvent $event): void
    {
        $User = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if ($User instanceof UserPlateform && Request::METHOD_POST === $method) {
            $otherNumber = [];

            for ($i = 0; $i < 4; $i++) {
                try {
                    $otherNumber[] = random_int(0, 9);
                } catch (\Exception $e) {
                    echo $e;
                }
            }

            $keySecret = password_hash(($User->getPhone() . '' . $User->getPassword() . '' . (new \DateTime())->format('Y-m-d H:i:s') . '' . implode("", $otherNumber)), PASSWORD_DEFAULT);

            if (strlen($keySecret) > 100) {
                $keySecret = substr($keySecret, 0, 99);
            }

            $User->setKeySecret($keySecret);
            $typeUser = $this->em->getRepository(TypeUser::class)->findOneBy(['id' => 2]);
            $User->setTypeUser($typeUser);

            // $this->em->persist($User);

            // $this->em->flush();
            $ExU = $this->em->getRepository(UserPlateform::class)->findOneBy(['codeParrainage' => $User->getCodeParrainage()]);
            if (
                $ExU

            ) {

                $this->parrain($ExU, $User);
            }

            $User->setCodeParrainage($this->generateCodeParainnage());
        }
    }
 

    public function parrain(UserPlateform $parrain, UserPlateform $fieul)

    {
     
    }

    public function generateCodeParainnage()
    {


        $chaine = 'bt';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 13; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(UserPlateform::class)->findOneBy(['codeParrainage' => $chaine]);
        if ($ExistCode) {
            return
                $this->generateCodeParainnage();
        } else {
            return $chaine;
        }
    }
}

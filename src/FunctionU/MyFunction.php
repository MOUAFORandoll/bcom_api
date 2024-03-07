<?php

namespace App\FunctionU;

use App\Entity\ColisObject;
use App\Entity\ControlMission;
use App\Entity\ListMissionBiker;
use App\Entity\Livraison;
use App\Entity\LivraisonKey;
use App\Entity\LivraisonOrdonnanceKey;
use App\Entity\Medicament;
use App\Entity\Mission;
use App\Entity\MissionSession;
use App\Entity\ObjectFile;
use App\Entity\Ordonnance;
use App\Entity\Pharmacie;
use App\Entity\UserObject;
use App\Entity\UserPlateform;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Exception;
use Symfony\Component\Mailer\MailerInterface;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Component\HttpFoundation\File\File as FileFile;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MyFunction
{
    public $emetteur = 'admin@prikado.com';
    public $host_serveur_socket;

    private $mailer;
    private $em;
    private $client;
    private
        $token    = "pk.tfLODRBooIKOCisurizG7NZNNYCO6wcGry0PranjBocWGKLNKFET3OYnGbR01WF0ftNrooMC5Z9iH8GfQv2llWNkkFCy0zk2wGkHdl6RD4LmTc3bBg3iauC7FdFTK";

    const
        BACK_END_URL =
        'https://api.bcom.cm';

    // BACK_END_URL =
    // 'http://192.168.43.134:8000';
    // BACK_END_URL =
    // 'http://192.168.1.102:8000';
    const
        PAGINATION = 14;
    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $client,
        MailerInterface $mailer,


    ) {

        $this->host_serveur_socket
            =/*  $_SERVER['REQUEST_SCHEME'] . ://.  $_SERVER['SERVER_ADDR'] */
            // 'http://192.168.1.102'
            'http://192.168.43.134'
            . ':3000';
        $this->client =
            $client;
        $this->mailer =
            $mailer;
        $this->em = $em;
    }
    function sendMail($to, $subject, $body)
    {
        $email = (new Email())
            ->from('hari.randoll@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html('<p>Lorem ipsum...</p>')
            ->text($body);
        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface  $e) {
            // Log or print the exception message for debugging
            return
                false;
        }
    }
    public function  getBackendUrl()
    {
        return $this::BACK_END_URL;
    }
    public function removeSpace(string $value)
    {
        return str_replace(' ', '', rtrim(trim($value)));
    }

    public function calculDistance($longU, $latU, $longL, $latL)
    {

        // convert from degrees to radians
        $latFrom = deg2rad($longU);
        $lonFrom = deg2rad($latU);
        $latTo = deg2rad($longL);
        $lonTo = deg2rad($latL);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return ceil($angle * 6371000 / 1000);
    }

    public function getUniqueNameColisName()
    {


        $chaine = 'colis';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
    }

    public function getUniqueNamUserName()
    {


        $chaine = 'colis';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(UserObject::class)->findOneBy(['src' => $chaine . 'jpg']);
        if ($ExistCode) {
            return
                $this->getUniqueNamUserName();
        } else {
            return $chaine;
        }
    }

    public function getUniqueNamCni()
    {


        $chaine = 'cni_biker';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(ObjectFile::class)->findOneBy(['src' => $chaine . 'jpg']);
        if ($ExistCode) {
            return
                $this->getUniqueNamUserName();
        } else {
            return $chaine;
        }
    }
    public function getUniqueNamCGrise()
    {


        $chaine = 'c_grise_biker';
        $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < 5; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        $ExistCode = $this->em->getRepository(ObjectFile::class)->findOneBy(['src' => $chaine . 'jpg']);
        if ($ExistCode) {
            return
                $this->getUniqueNamUserName();
        } else {
            return $chaine;
        }
    }
    function formatMission(Mission $da)
    {

        return  [

            'id' => $da->getId(),
            'libelle' => $da->getLibelle(),
            'description' => $da->getDescription(),
            'nbre_point' => $da->getNbrePoint(),
            'date_created'
            => date_format($da->getDateCreated(), 'Y-m-d H:i'),

            'status' => $da->isStatus(),

        ];
    }
    function formatMissionForUser(ListMissionBiker $da)
    {

        return  [

            'id' => $da->getMission()->getId(),
            'libelle' => $da->getMission()->getLibelle(),
            'date_created' =>
            date_format($da->getMission()->getDateCreated(), 'Y-m-d H:i'),

            'description' => $da->getMission()->getDescription(),
            'nbre_point' => $da->getMission()->getNbrePoint(),
            'status' => $da->isStatus(),
            'nbre_session' => count($da->getMissionSessions()),

        ];
    }
    function formatBiker(UserPlateform $da)
    {

        return  [

            'id' => $da->getId(),
            'name' => $da->getNomComplet(),

            'image' => '',
            'phone' =>  $da->getPhone(),

        ];
    }
    function formatMissionControl(ControlMission $da)
    {

        return  [

            'id' => $da->getId(),
            'note' => $da->getNote(),
            'mission' => $this->formatMissionForUser($da->getBikerMission()->getMissionbiker()),
            'biker' => $this->formatBiker($da->getBikerMission()->getMissionbiker()->getBiker()),
            'date_created' =>
            date_format($da->getDateCreated(), 'Y-m-d H:i'),
            'biker_position' => $da->getBikerMission()->getMissionbiker()->getMissionSessions()->last()->getPointLocalisations(),

        ];
    }
    function formatMissionSession(MissionSession $da)
    {

        return  [

            'id' => $da->getId(),

            'date_start' =>
            date_format($da->getDateCreated(), 'Y-m-d H:i'),

            'date_end' => $da->getDateEnd() == null ? null :
                date_format($da->getDateEnd(), 'Y-m-d H:i'),
            'end_mission' => $da->isEndMission(),

        ];
    }
    function formatMissionSessionN(MissionSession $da)
    {

        return  [

            'id' => $da->getId(),

            'date_start' =>
            date_format($da->getDateCreated(), 'Y-m-d H:i'),

            'date_end' => $da->getDateEnd() == null ? null :
                date_format($da->getDateEnd(), 'Y-m-d H:i'),
            'end_mission' => $da->isEndMission(),
            'biker' => $this->formatBiker($da->getControlMissions()->last()->getMissionbiker()->getBiker()),



        ];
    }
    public function emitForLivraison($livraison)
    {

        // $this->Socekt_Emit('livraison', [
        //     'recepteur' => $livraison->getInitiatedUser()->getKeySecret(),
        //     'data'
        //     =>
        //     $this->formatLivraison($livraison)

        // ]);
        // $this->Socekt_Emit('livraison', [
        //     'recepteur' => $livraison->getLivreur()->getKeySecret(),
        //     'data'
        //     =>
        //     $this->formatLivraison($livraison)

        // ]);
    }
    public function emitForLivraisonFinish($livraison, $message)
    {

        $this->Socekt_Emit('livraison_finish', [
            'recepteur' => $livraison->getInitiatedUser()->getKeySecret(),
            'data'
            =>
            $message

        ]);
    }


    public function Socekt_Emit($canal, $data)
    {



        $first =   $this->client->request('GET',   $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&t=N8hyd6w");
        $content = $first->getContent();
        $index = strpos($content, 0);
        $res = json_decode(substr($content, $index + 1), true);
        $sid = $res['sid'];
        $this->client->request('POST',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&sid={$sid}", [
            'body' => '40'
        ]);

        $dataEmit = [$canal, json_encode($data)];

        // $this->client->request('POST',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42["%s", %s]', $userID, json_encode($dataEmit))
        // ]);
        // $this->client->request('POST',  $this->host_serveur_socket ."/socket.io/?EIO=4&transport=polling&sid={$sid}", [
        //     'body' => sprintf('42%s',  json_encode($dataSign))
        // ]);
        $this->client->request('POST',  $this->host_serveur_socket . "/socket.io/?EIO=4&transport=polling&sid={$sid}", [
            'body' => sprintf('42%s',  json_encode($dataEmit))
        ]);
    }
}

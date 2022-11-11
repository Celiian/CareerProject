<?php
// src/Service/MessageGenerator.php
namespace App\Service;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailSender
{

    public function sendMail(MailerInterface $mailer, string $sender, string $receiver, string $subject, string $text, string $html): bool
    {

        $email = (new Email())
            ->from($sender)
            ->to($receiver)
            ->subject($subject)
            ->text($text)
            ->html($html);

        try {
            $mailer->send($email);
            return true;

        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }


}
<?php
namespace GDO\Helpdesk\Method;
use GDO\File\GDT_File;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Helpdesk\GDO_Ticket;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\User\GDO_User;
use GDO\Mail\Mail;
use GDO\UI\GDT_Message;
use GDO\Comment\GDO_Comment;
use GDO\Helpdesk\GDO_TicketMessage;
use GDO\UI\GDT_Link;
use GDO\Helpdesk\Module_Helpdesk;

final class OpenTicket extends MethodForm
{
    public function createForm(GDT_Form $form)
    {
        $tickets = GDO_Ticket::table();
        $form->addFields(array(
            $tickets->gdoColumn('ticket_title'),
            GDT_Message::make('comment_message'),
            GDT_File::make('comment_file'),
            GDT_Submit::make(),
            GDT_AntiCSRF::make(),
        ));
        
        if (!Module_Helpdesk::instance()->cfgAttachments())
        {
            $form->removeField('');
        }
    }
    
    public function formValidated(GDT_Form $form)
    {
        $ticket = GDO_Ticket::blank($form->getFormData())->insert();
        $comment = GDO_Comment::blank($form->getFormData())->insert();
        GDO_TicketMessage::blank(array(
            'comment_id' => $comment->insert(),
            'comment_object' => $ticket->getID(),
        ))->insert();
        $this->sendMail($ticket, $comment);
        return $this->message('msg_helpdesk_ticket_created');
    }
        
    private function sendMail(GDO_Ticket $ticket, GDO_Comment $comment)
    {
        foreach (GDO_User::withPermission('staff') as $user)
        {
            $this->sendMailTo($user, $ticket);
        }
    }

    private function sendMailTo(GDO_User $user, GDO_Ticket $ticket, GDO_Comment $comment)
    {
        $mail = Mail::botMail();
        $username = $user->displayNameLabel();
        $sitename = sitename();
        $creator = $ticket->getCreator()->displayNameLabel();
        $title = $ticket->getTitle();
        $message = $comment->displayMessage();
        $linkClaim = GDT_Link::make()->href(url('Helpdesk', 'Claim', "&token={$ticket->gdoHashcode()}"))->re;
        $mail->setSubject(tusr($user, 'mail_subj_helpdesk_ticket_created', [$sitename]));
        $args = [$username, $sitename, $creator, $title, $message, $linkClaim];
        $mail->setBody(tusr($user, 'mail_body_helpdesk_ticket_created', $args));
        $mail->sendToUser($user);
    }
    
}

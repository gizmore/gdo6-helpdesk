<?php
namespace GDO\Helpdesk;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_CreatedBy;
use GDO\Date\GDT_DateTime;
use GDO\DB\GDT_String;
use GDO\User\GDT_User;
use function foo\func;
use GDO\Comment\CommentedObject;
use GDO\User\GDO_User;
use GDO\UI\GDT_Title;
/**
 * Helpdesk ticket.
 * Use Comment module for messaging.
 * @author gizmore
 */
final class GDO_Ticket extends GDO
{
    use CommentedObject;
    public function gdoCommentTable() { return GDO_TicketMessage::table(); }
    
    public function gdoTableName() { return 'gdo_helpdesk_ticket'; }
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('ticket_id'),
            GDT_Title::make('ticket_title')->max(96),
            # Customer
            GDT_CreatedAt::make('ticket_created_at'),
            GDT_CreatedBy::make('ticket_created_by'),
            # Worker
            GDT_DateTime::make('ticket_claimed_at'),
            GDT_User::make('ticket_claimed_by'),
            # Closed
            GDT_DateTime::make('ticket_closed_at'),
            GDT_User::make('ticket_closed_by'),
        );
    }
    
    ##############
    ### Getter ###
    ##############
    public function getTitle() { return $this->getVar('ticket_title'); }
    /**
     * @return GDO_User
     */
    public function getCreator() { return $this->getValue('ticket_created_by'); }
    public function getCreatedAt() { return $this->getVar('ticket_created_at'); }
    
}
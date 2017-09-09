<?php
namespace GDO\Helpdesk;
use GDO\Core\GDO_Module;
use GDO\Template\GDT_Bar;
use GDO\UI\GDT_Link;

final class Module_Helpdesk extends GDO_Module
{
    public function onLoadLanguage()
    {
        $this->loadLanguage('lang/helpdesk');
    }
    
    public function getClasses()
    {
        return array(
            'GDO\\Helpdesk\\GDO_Ticket',
            'GDO\\Helpdesk\\GDO_TicketMessage',
        );
    }
    
    public function hookRightBar(GDT_Bar $bar)
    {
        $bar->addField(GDT_Link::make('link_helpdesk')->href(href('Helpdesk', 'OpenTicket')));
    }
    
}

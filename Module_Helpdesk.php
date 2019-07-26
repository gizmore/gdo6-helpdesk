<?php
namespace GDO\Helpdesk;
use GDO\Core\GDO_Module;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\DB\GDT_Checkbox;

final class Module_Helpdesk extends GDO_Module
{
	##############
	### Module ###
	##############
	public function getDependencies()
	{
		return ['Comment'];
	}
	
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
    
    #############
    ### Hooks ###
    #############
    public function hookRightBar(GDT_Bar $bar)
    {
        $bar->addField(GDT_Link::make('link_helpdesk')->href(href('Helpdesk', 'OpenTicket')));
    }
 
    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
    	return array(
    		GDT_Checkbox::make('helpdesk_attachments')->initial('1'),
    	);
    }
    public function cfgAttachments() { return $this->getConfigValue('helpdesk_attachments'); }

}

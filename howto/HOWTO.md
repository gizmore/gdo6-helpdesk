# gdo6 how to: Write a ticketing Helpdesk module.

## 1. The Module

    File: Module_Helpdesk.php(https://)

    Inherit from GDO_Module.
    Name your module like Module_Foldername. e.g. Module_Helpdesk.php
    Modules can have templates, hooks and methods.
    We currently do nothing in our module. Later we will define tables and a language file. 
    
    namespace GDO\Helpdesk;
    final class Module_Helpdesk extends GDO_Module {}
    

## 2. The Ticket Table

    We create our first basic approach of a database design.
    DatabaseObjects and Tables use the same class; GDO()
    We create a class and extend it.
    
    File: GDO_Ticket.php(https://)
    
    The resulting database table name would be gdo_ticket.
    It is not required to use any naming convention here.
    We override the default table name and define columns in gdoColumns().
    Our table contains 3 timestamps and users for customer, worker and closer.
    
    namespace GDO\Helpdesk;
    final class GDO_Ticket extends GDO
    {
        public function gdoTableName() { return 'gdo_helpdesk_ticket'; }
        public function gdoColumns()
        {
            return array(
                GDT_AutoInc::make('ticket_id'),
                GDT_String::make('ticket_title')->max(96),
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
    }
    
    To get the table installed together with the module, the module can return it in getClasses()
    
    File: Module_Helpdesk.php(https://)
 
    module Helpdesk extends GDO_Module
    {
        public function getClasses()
        {
            return array(
                'GDO\\Helpdesk\\GDO_Ticket',
            );
        }
    }
    

## 3. Ticket Creation

We create a method with a simple form.

    File: OpenTicket.php()
    
We will override GDO\Form\MethodForm, as it defaults to transactions on post, and has some basic GDT_Form handling code.
MethodForm expects us to overload the createForm($form) method, and add gdt fields to the formd.

    namespace GDO\Helpdesk\Method;
    final class OpenTicket extends GDO_MethodForm
    {
        public function createForm(GDT_Form $form)
        {
            $tickets = GDO_Ticket::table();
            $form->addFields(array(
                $tickets->gdoColumn('ticket_title'),
                GDT_Submit::make(),
                GDT_AntiCSRF::make(),
            ));
        }
    }
    
We added the ticket title, a default submit button, and a csrf token to the form.
When the form is successfully validated, a onSubmit_BTNNAME name method is called.
In MethodForm we will override formValidated, which simply is called in onSubmit_submit.

    public function formValidated(GDT_Form $form)
    {
        $ticket = GDO_Ticket::blank($form->getFormData())->insert();
        return $this->message('msg_helpdesk_ticket_created');
    }
 
## 4. Language File
 
We create a language file for the message above.

    File: helpdesk_en.php()
    
    return array(
        'msg_helpdesk_ticket_created' => 'Your helpdesk ticket has been created.',
    );
    
Now we load the file within the module

    File: Module_Helpdesk.php(https://)

    public function onLoadLanguage()
    {
        $this->loadLanguage('lang/helpdesk');
    }
       

## 5. Hook into sidebar.

Let us hook the OpenTicket method into the right sidebar.
GDO_Module::onInitSidebar() is the right place to do so.
By convention, the left bar is for content, and the right bar for private functions.
I think opening a ticket is more a private function, so lets hook there.

    File: Module_Helpdesk.php(https://)
    
    public function onInitSidebar()
    {
    	  $bar = GDT_Page::$INSTANCE->rightNav;
        $bar->addField(GDT_Link::make('link_helpdesk_open_ticket')->href(href('Helpdesk', 'OpenTicket')));
    }

## 6. Adding ticket message and attachments.

We will use the comments module to add message functionality to our helpdesk tickets.
Read about the comments module here: https://github.com/gizmore/gdo6-comment


## 7. Optional Attachments

We will add a config var to the module if we allow tickets to contain attachments.
This is quite easily done by overloading getConfig in the module.

    public function getConfig()
    {
      	return array(
     		GDT_Checkbox::make('helpdesk_attachments')->initial('1'),
     	);
    }

This will create a boolean with the default value set to yes.
You can access your setting under Admin->Modules->Helpdesk->Configure.


# gdo6 how to: Write a ticketing Helpdesk module.

## 1. The Database Tables and GDO.

We will use 1 table for the tickets.
To create a table define a class extending GDO, and return an array of gdo's GDO_Base types
The table features these gdo fields:
GDO_AutoInc
GDO_CreatedBy
GDO_CreatedAt

Severety will be a dropdown of "high", "medium", "low".
Tickets can be closed. This is a filtered state
Tickets can be grabbed by users of the staff group.
Tickets who have an updated_at older than 1 week will get closed


### 1.1 The GDT_Ticket table.

namespace GDO\Helpdesk;
class GDT_Ticket extends GDO
{

}

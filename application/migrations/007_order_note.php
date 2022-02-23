<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_order_note extends CI_Migration
{
    public function up()
    {

        /* adding new fields in orders table */
        $fields = array(
            'notes' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => TRUE,
                'after' => 'otp'
            )
        );
        $this->dbforge->add_column('orders', $fields);

        /* adding new fields in promo_codes table */
        $fields = array(
            'image' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => TRUE,
                'after' => 'no_of_repeat_usage'
            )
        );
        $this->dbforge->add_column('promo_codes', $fields);
	
		/* adding new table order_tracking */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'order_id' => [
                'type'           => 'INT',
                'constraint'     => '11'
            ],
            'courier_agency' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
                'NULL'           => TRUE
            ],
            'tracking_id' => [
                'type'           => 'VARCHAR',
                'constraint'     => '120',
                'NULL'           => TRUE
            ],
            'url' => [
                'type'           => 'VARCHAR',
                'constraint'     => '256',
                'NULL'           => TRUE
            ],
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_tracking');
    }

    public function down()
    {
        // Drop table 
        //   $this->dbforge->drop_table('order_bank_transfer');
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');
        $this->dbforge->drop_table('order_tracking');
        $this->dbforge->drop_column('orders', 'notes');
        $this->dbforge->drop_column('promo_codes', 'image');
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_orders_alter_table extends CI_Migration
{
    public function up()
    {

        /* adding new fields in orders table */
        $fields = array(
            'is_delivery_charge_returnable' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'DEFAULT'    => '0',
                'after' => 'delivery_charge'
            ),
            'address_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'NULL'     => TRUE,
                'after' => 'delivery_boy_id'
            )
        );
        $this->dbforge->add_column('orders', $fields);

        /* adding new fields in products table */
        $fields = array(
            'is_prices_inclusive_tax' => array(
                'type' => 'INT',
                'constraint' => '11',
                'DEFAULT'     => '0',
                'after' => 'total_allowed_quantity'
            )
        );
        $this->dbforge->add_column('products', $fields);
    }

    public function down()
    {
        // Drop table 
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');
        $this->dbforge->drop_column('orders', 'is_delivery_charge_returnable');
        $this->dbforge->drop_column('products', 'is_prices_inclusive_tax');
        $this->dbforge->drop_column('orders', 'address_id');
    }
}

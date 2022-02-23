<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_products_alter_table extends CI_Migration
{
    public function up()
    {

		/* adding new fields in products table */
        $fields = array(
            'minimum_order_quantity' => array(
				'type' => 'INT',
				'constraint' => '11',
				'DEFAULT'    => '1',
				'after' => 'indicator'
			),
            'quantity_step_size' => array(
                'type' => 'INT',
				'constraint' => '11',
				'DEFAULT'     => '1',
				'after' => 'minimum_order_quantity'
			),
            'cod_allowed' => array(
                'type' => 'INT',
				'constraint' => '11',
				'DEFAULT'     => '1',
				'after' => 'indicator'
			),
        );
        $this->dbforge->add_column('products', $fields);
    }

    public function down()
    {
        // Drop table 
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');
        $this->dbforge->drop_column('products', 'minimum_order_quantity');
        $this->dbforge->drop_column('products', 'quantity_step_size');
        $this->dbforge->drop_column('products', 'cod_allowed');
    }
}
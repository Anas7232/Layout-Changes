<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_popular_categories extends CI_Migration
{
    public function up()
    {

        /* adding new fields in categories table */
        $fields = array(
            'clicks' => array(
                'type' => 'INT',
                'constraint' => '11',
                'DEFAULT'    => '0',
                'after' => 'status'
            )
        );
        $this->dbforge->add_column('categories', $fields);

        /* adding new fields in order_bank_transfer table */
        $fields = array(
            'status' => array(
                'type' => 'TINYINT',
                'constraint' => '2',
                'DEFAULT'    => '0',
                'after' => 'attachments',
                'comment' => '0:pending|1:rejected|2:accepted',
            )
        );
        $this->dbforge->add_column('order_bank_transfer', $fields);

        /* adding new fields in products table */
        $fields = array(
            'product_identity' => array(
                'type'           => 'VARCHAR',
                'constraint'     => '50',
                'NULL'           => TRUE,
                'after' => 'id'
            )
        );
        $this->dbforge->add_column('products', $fields);
		
		/* modifying column */
		$this->db->query("ALTER TABLE `attribute_values` CHANGE `swatche_type` `swatche_type` VARCHAR(512) NULL DEFAULT NULL;");
		$this->db->query("ALTER TABLE `attribute_values` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
		$this->db->query("ALTER TABLE `taxes` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
    }

    public function down()
    {
        // Drop table 
        //   $this->dbforge->drop_table('order_bank_transfer');
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');
        $this->dbforge->drop_column('categories', 'clicks');
        $this->dbforge->drop_column('products', 'product_identity');
        $this->dbforge->drop_column('order_bank_transfer', 'status');
    }
}

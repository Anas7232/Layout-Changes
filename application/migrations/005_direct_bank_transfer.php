<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_direct_bank_transfer extends CI_Migration
{
    // and I think user_type_id ni jagya a simply user_id rakhi daia to chalse
    public function up()
    {
        /* adding new table order_bank_transfer */
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
            'attachments' => [
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => TRUE
            ],
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_bank_transfer');

        /* adding new table zipcodes */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'zipcode' => [
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => TRUE
            ],
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('zipcodes');

        /* adding new fields in areas table */
        $fields = array(
            'zipcode_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'DEFAULT'    => '0',
                'after' => 'city_id'
            )
        );
        $this->dbforge->add_column('areas', $fields);

        /* adding new fields in products table */
        $fields = array(
            'deliverable_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'DEFAULT'    => '1',
                'after' => 'description',
                'comment' => '(0:none, 1:all, 2:include, 3:exclude)',
            ),
            'deliverable_zipcodes' => array(
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => TRUE,
                'after' => 'deliverable_type'
            )
        );
        $this->dbforge->add_column('products', $fields);

        /* altering the pincode field at addresses table */
        $fields = array(
            'pincode' => array(
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => TRUE
            ),
        );
        $this->dbforge->modify_column('addresses', $fields);
    }

    public function down()
    {
        // Drop table 
        //   $this->dbforge->drop_table('order_bank_transfer');
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');
        $this->dbforge->drop_table('order_bank_transfer');
        $this->dbforge->drop_column('areas', 'zipcode_id');
        $this->dbforge->drop_table('zipcodes');
        $this->dbforge->drop_column('products', 'deliverable_type');
        $this->dbforge->drop_column('products', 'deliverable_zipcodes');
    }
}
